<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 15.05.2018
 * Time: 14:32
 */

namespace app\controllers;

use app\components\functions\emails;
use app\components\functions\functions;
use app\models\DriverLicense;
use Yii;
use app\models\Profile;
use app\models\SignupCarOwnerForm;
use yii\web\Controller;
use yii\filters\AccessControl;

class CarOwnerController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ]
                ]
            ]
        ];
    }


    public function  actionCreate(){
        $modelStart = new SignupCarOwnerForm();
        $modelProfile = Profile::find()->where(['id_user' => \Yii::$app->user->id])->one();
        if($modelProfile){
            $modelStart->phone2 = $modelProfile->phone2;
            $modelStart->email2 = $modelProfile->email2;
            $modelStart->bithday = $modelProfile->bithday;
            $modelStart->id_user = $modelProfile->id_user;
//            $modelStart->passport_number = $modelProfile->passport->number;
//            $modelStart->country = $modelProfile->passport->country;
//            $modelStart->passport_date = $modelProfile->passport->date;
//            $modelStart->passport_place = $modelProfile->passport->place;
            $modelStart->is_driver = $modelProfile->is_driver;
            $modelStart->reg_address = $modelProfile->reg_address;
            $modelStart->photo = $modelProfile->photo;

        }
        else $modelProfile = new Profile();

        if($modelStart->load(\Yii::$app->request->post())){
            if ($modelProfile = $modelStart->saveProfile()) {

                functions::setFlashSuccess('Поздравляем с успешной регистрацией. 
                   Добавьте транспортное средство в личном кабинете и получайте заказы!');
                emails::sendAfterCarOwnerRegistration($modelProfile->id_user);
                if(!$modelProfile->is_driver){
                    return $this->redirect('/driver');
                }
                return $this->redirect('/vehicle');
            }
            functions::setFlashWarning('Ошибка на сервере. Попробуйте позже.');
        }
        if(!$modelProfile) $modelProfile = new Profile();
        return $this->render('create', [
            'modelStart' => $modelStart,
            'modelProfile' => $modelProfile,
        ]);
    }

    public function actionValidatePassport()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $model = new SignupCarOwnerForm();
            if($model->load(Yii::$app->request->post()))
                return \yii\widgets\ActiveForm::validate($model);
        }
        throw new \yii\web\BadRequestHttpException('Bad request!');
    }
}