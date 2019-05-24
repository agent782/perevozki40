<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 11.01.2018
 * Time: 17:29
 */

namespace app\controllers;

use app\models\Company;
use app\models\OrderOLD;
use app\models\Passport;
use app\models\Profile;
use app\models\XprofileXcompany;
use Codeception\Lib\Connector\Yii2;
use Yii;
use app\components\functions\functions;
use app\models\signUpClient\SignUpClientFormStart;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

class ClientController extends Controller
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

    public function actionIndex()
    {
        return $this->render('index', compact([]));
    }

//    public function actionAddcompany(){
//        $modelCompany = new Company();
//        $modelProfile = Profile::findOne(Yii::$app->user->getId());
//
//        if ($modelCompany->load(Yii::$app->request->post())){
//            if(!Company::find()->where(['inn' => $modelCompany->inn])->count()){
//                if ($modelCompany->save()) {
//                    $modelCompany->link('profiles', $modelProfile);
//                    return 'Create and add';
//                }else return 'ERROR';
//            }else {
//                $modelCompany = Company::find()->where(['inn' => $modelCompany->inn])->one();
//                if(XprofileXcompany::find()->where(['id_profile' => Yii::$app->user->getId()])->andWhere(['id_company' => $modelCompany->id])->count()){
//                    return 'Уже добавлено';
//                }else {
////                    $modelProfile = Profile::findOne(Yii::$app->user->getId());
//                    $modelCompany->link('profiles', $modelProfile);
//                    return 'Add company to Profile';
//                }
//            }
//        return 'ERROR';
////            return $this->render('test', compact(['modelCompany']));
//        }
//        return $this->render('addcompany', compact(['modelCompany']));
//    }


    public function actionValidateAddCompany()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $model = new Company();
            if($model->load(Yii::$app->request->post()))
                return \yii\widgets\ActiveForm::validate($model);
        }
        throw new \yii\web\BadRequestHttpException('Bad request!');

    }
}