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
use app\models\User;
use Yii;
use app\components\functions\functions;
use app\models\signUpClient\SignUpClientFormStart;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

class UserController extends Controller
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

    public function actionIndex(){
        $modelUser = functions::findUser(\Yii::$app->user->identity->getId());

        return $this->render('index', compact(['modelUser']));
    }

    public function actionSignupClient(){
        $modelStart = new SignUpClientFormStart();
        if($modelStart->load(Yii::$app->request->post())){
            if ($modelProfile = $modelStart->saveProfile()) {
//                return $this->render('signupClient2', compact(['modelProfile']));
                return $this->redirect('/client/addcompany');
            }
            else {
//                return 1;
                return $this->render('signupClientStart', compact(['modelStart', 'modelProfile','modelPassport']));
            }
        }
        else{
            return $this->render('signupClientStart', compact(['modelStart']));
        }

    }

    public function actionValidatePassport()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $model = new SignUpClientFormStart();
            if($model->load(Yii::$app->request->post()))
                return \yii\widgets\ActiveForm::validate($model);
        }
        throw new \yii\web\BadRequestHttpException('Bad request!');
    }

    public function actionAddpushallid($user_id = null){
        return var_dump(Yii::$app->request->get());
        if (Yii::$app->request->get('pushalluserid')) {
            if(!$user_id){
                $user = Yii::$app->user->identity;
            } else {
                $user = User::find()->where(['id' => $user_id])->one();
            }
            if(!$user)return 'test';
            if(is_array($user->push_ids)) {
                $pids = [];
            } else {
                $pids = $user->push_ids;
            }
            $pids [] = Yii::$app->request->get('pushalluserid');
            $user->push_ids = $pids;
            return var_dump($user->push_ids);
            $user->save();
            return var_dump($user->push_ids);
        }
        return $this->redirect('user');
        
    }
}