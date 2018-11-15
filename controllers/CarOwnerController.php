<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 15.05.2018
 * Time: 14:32
 */

namespace app\controllers;

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
            $modelStart->passport_number = $modelProfile->passport->number;
            $modelStart->country = $modelProfile->passport->country;
            $modelStart->passport_date = $modelProfile->passport->date;
            $modelStart->passport_place = $modelProfile->passport->place;

            $modelStart->reg_address = $modelProfile->reg_address;
            $modelStart->photo = $modelProfile->photo;

        }
        if(!$modelProfile) $modelProfile = new Profile();
        if($modelStart->load(\Yii::$app->request->post()) && $modelStart->assignAgreement){
            if ($modelProfile = $modelStart->saveProfile()) {
//                return $this->render('signupClient2', compact(['modelProfile']));
                return $this->redirect('/');
            }
            else {
                return $this->render('create', [
                    'modelStart' => $modelStart,
                    'modelProfile' =>$modelProfile
                ]);
            }
        }
        if(!$modelStart->assignAgreement) Yii::$app->session->setFlash('warning', 'Ознакомьтесь с Соглашением об использовании сервиcа perevozki40.ru');
        return $this->render('create', [
           'modelStart' => $modelStart,
            'modelProfile' => $modelProfile
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