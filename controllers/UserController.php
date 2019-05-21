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
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

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
                functions::setFlashSuccess('Спасибо, ' . $modelProfile->name . ' ' . $modelProfile->patrinimic
                    . '! Надеемся на долгосрочное сотрудничество!');
                return $this->redirect('/');
            }
            else {
                functions::setFlashWarning('Ошибка на сервере. Попробуйте позже...');
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

        $pushalluserid = Yii::$app->request->get('pushalluserid');
//        return var_dump(Yii::$app->request->get());
        if ($pushalluserid) {
            if(!$user_id){
                $user = User::findOne(Yii::$app->user->id);
            } else {
                $user = User::find()->where(['id' => $user_id])->one();
            }
            if(!$user){
                functions::setFlashWarning('Ошибка на сервере.');
                return $this->redirect('/user');
            }
            if(!is_array($user->push_ids)) {
                $pids = [];
                $pids [] = $pushalluserid;
            } else {
                $hasId=0;
                foreach ($user->push_ids as $push_id){
                    if($push_id == $pushalluserid) $hasId = 1;
                }
                $pids = $user->push_ids;
                if(!$hasId) $pids [] = $pushalluserid;
            }
            $user->push_ids = $pids;
            $user->scenario = $user::SCENARIO_SAVE;
            if($user->save()) {
                functions::setFlashSuccess('Вы подписались на push-уведомления.');
            } else {
//                return var_dump($user->getErrors());
                functions::setFlashWarning('Ошибка на сервере.');
            }
//            return var_dump(Yii::$app->request->get());
            return $this->redirect('/user');
        }
        functions::setFlashWarning('Ошибка на сервере.');
        return $this->redirect('/user');
    }

    public function actionFindUser($redirect, $id_order = null, $id_user = null, $redirect2 = null){
        $user = new User();
        $profile = new Profile();
        if($user->load(Yii::$app->request->post()) && $profile->load(Yii::$app->request->post())) {

            if (User::find()->where(['username' => $user->username])->one()) {
                $tmpUser = User::findOne(['username' => $user->username]);
                $tmpUser->email = $user->email;
                $tmpProfile = $tmpUser->profile;
                $tmpProfile->name = $profile->name;
                $tmpProfile->surname = $profile->surname;
                $tmpProfile->patrinimic = $profile->patrinimic;
                $tmpProfile->phone2 = $profile->phone2;
                $tmpProfile->email2 = $profile->email2;

                $user = $tmpUser;
                $profile = $tmpProfile;
                $user->scenario = $user::SCENARIO_SAVE;
                $profile->scenario = $profile::SCENARIO_SAFE_SAVE;
                if ($user->save() && $profile->save()) {
                    functions::setFlashSuccess('Пользовватель сохранен.');
                } else {
                    functions::setFlashWarning('Ошибка при сохранении пользователя');
                }
            } else {
                $user->setPassword(rand(10000000, 99999999));
                $user->generateAuthKey();

                $user->scenario = $user::SCENARIO_SAVE;
                $profile->scenario = $profile::SCENARIO_SAFE_SAVE;

                if ($user->save()) {
                    $profile->id_user = $user->id;
                    if($profile->save()){
                        functions::setFlashSuccess('Пользовватель добавлен.');
                    }else {
                        functions::setFlashWarning('Ошибка при сохранении пользователя');
                    }
                }else {
                    functions::setFlashWarning('Ошибка при сохранении пользователя');
                }
            }
            return $this->redirect([$redirect,
                'id_user' => $user->id,
                'id_order' => $id_order,
                'redirect' => $redirect2
            ]);
        }
        return $this->render('find-user',[
            'user' => $user,
            'profile' => $profile
        ]);
    }

    public function actionAutocomplete($term){
        if(Yii::$app->request->isAjax){
            $profiles = Profile::find()->all();
            $res = [];
            foreach ($profiles as $profile) {
                if(strpos($profile->phone, $term)!==false || strpos($profile->phone2, $term)!==false){
                    $res[] = [
                        'id' => $profile->id_user,
                        'phone' => $profile->phone,
                        'phone2' => $profile->phone2,
                        'email' => $profile->email,
                        'email2' => $profile->email2,
                        'name' => $profile->name,
                        'surname' => $profile->surname,
                        'patrinimic' => $profile->patrinimic,
                        'value' => $profile->phone . ' (' . $profile->phone2 . ') ' . $profile->fioFull . ' (ID ' . $profile->id_user . ')',
                        'label' => $profile->phone . ' (' . $profile->phone2 . ') ' . $profile->fioFull . ' (ID ' . $profile->id_user . ')',
                        'companies' => ArrayHelper::map($profile->companies, 'id', 'name'),
                        'info' => $profile->profileInfo . ' ' . $profile->getRating()
                    ];
                }
            }
            echo Json::encode($res);
//        echo Json::encode([1111,22222,33333,44444]);
        }
    }
}