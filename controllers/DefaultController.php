<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 11.12.2017
 * Time: 12:57
 */

namespace app\controllers;
use app\components\functions\emails;
use app\components\functions\functions;
use app\models\LoginForm;
use app\models\Profile;
use app\models\SignupPhoneForm;
use app\models\User;
use app\models\VerifyPhone;
use app\models\SignupUserForm;
use Yii;
use app\models\auth_item;
use yii\captcha\NumericCaptcha;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Sms;
use yii\web\Session;

//use yii\web\Controller;

class DefaultController extends Controller
{

    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction'
            ]
        ];
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],

        ];
    }



    public function actionIndex(){
//        $auth = new auth_item();
        return $this->render('index');
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            if($urlBack = Yii::$app->request->get('urlBack')){
                return $this->redirect($urlBack);
            }
            return $this->redirect('/');
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect('/default/login');
    }

    //Регистрация
    public function actionSignup(){

        $modelUser = new User();
        $modelProfile = new Profile();
        $modelVerifyPhone = new VerifyPhone();
        $modelSignupUserForm = new SignupUserForm();
        $modelPhoneForm = new SignupPhoneForm();
        $session = Yii::$app->session;
        if($session->get('modelProfile')) $modelProfile = $session->get('modelProfile');

       //Для аякс валидации на уникальность телефона
//        if (Yii::$app->request->isAjax) {
//            Yii::$app->response->format = Response::FORMAT_JSON;
//            return ActiveForm::validate($modelUser);
//        }
        switch (Yii::$app->request->post('button')){
            case 'signup1':
                $session->remove('modelUser');
                if($modelProfile->load(Yii::$app->request->post())){
                    $session->remove('modelVerifyPhone');
                    $session->set('modelProfile', $modelProfile);
                    $session->set('modelUser', $modelUser);
                    return $this->render('signup2', compact(['modelVerifyPhone', 'modelProfile', 'modelUser', 'modelPhoneForm']));
                }
                break;
            case 'signup2':
                if(!$session->has('modelProfile') || !$session->has('modelUser')) break;
                if($session->get('modelUser')) $modelUser = $session->get('modelUser');

                $session->remove('modelVerifyPhone');

                if ($modelUser->load(Yii::$app->request->post())) {

                    if($session->get('timeout_new_code') > time()) {
//                        return 1 . $session->get('timeout_new_code');
                        functions::setFlashWarning('Повторная отправка смс-кода возможна через 5 минут');
                        return $this->render('signup2', compact(['modelVerifyPhone', 'modelProfile', 'modelUser']));
                    }
                    $modelVerifyPhone->generateCode();

                    $session->set('timeout_new_code', time()+300);
//                    $modelVerifyPhone->generateCode();

//                    $session->set('timeout_new_code', time()+30);

                    $session->set('modelVerifyPhone', $modelVerifyPhone);
                    $session->set('modelUser', $modelUser);
                    return $this->render('signup3', compact(['modelVerifyPhone', 'modelProfile', 'modelUser']));
                }
                return $this->render('signup2', compact(['modelVerifyPhone', 'modelProfile', 'modelUser']));
                break;
            case 'signup3':
                if(!$session->has('modelProfile')
                    || !$session->has('modelUser')
                    || !$session->has('modelVerifyPhone')) break;

                $modelVerifyPhone = $session->get('modelVerifyPhone');
                $modelUser = $session->get('modelUser');
                //отправить код по смс

                if ($modelVerifyPhone->load(Yii::$app->request->post())) {
                    $session->set('modelUser', $modelUser);
//
                    return $this->render('signup4', compact(['modelVerifyPhone', 'modelProfile', 'modelUser', 'modelSignupUserForm']));
                }

                return $this->render('signup3', compact(['modelVerifyPhone', 'modelProfile', 'modelUser']));
                break;
            case 'signup4':

                if($modelSignupUserForm->load(Yii::$app->request->post())){

                    $modelUser = $session->get('modelUser');

//                    return var_dump(Yii::$app->getUser()->login($modelUser));
                    $modelProfile = $session->get('modelProfile');
                    if ($modelUser = $modelSignupUserForm->signup($modelUser)) {
                        if (Yii::$app->getUser()->login($modelUser)) {
                            $modelProfile->id_user = $modelUser->id;
                            if ($modelProfile->save()) {
//                                $session->removeAll();
                                functions::setFlashSuccess('Поздравляем с успешной регистрацией!');
                                $session->remove('modelUser');
                                $session->remove('modelProfile');
                                $session->remove('modelVerifyKey');
                                $session->remove('modelSignupUserForm');
                                emails::sendAfterUserRegistration($modelProfile->id_user);
                                return $this->redirect('/');
                            }
                            functions::setFlashWarning('ОШИБКА НА СЕРВЕРЕ. Попробуйте позже.');

                            return $this->render('signup4', compact(['modelVerifyPhone', 'modelProfile', 'modelUser', 'modelSignupUserForm']));
                        }
                    }
                    functions::setFlashWarning('ОШИБКА НА СЕРВЕРЕ. Попробуйте позже.');

                    return $this->redirect('/default/signup');
                }
                break;
            default:
                break;
        }


//        Yii::$app->session->removeAll();
        return $this->render('signup', compact(['modelProfile']));
    }
//Для аякс валидации на уникальность телефона
    public function actionValidatePhone()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $model = new User();
            if($model->load(Yii::$app->request->post()))
                return \yii\widgets\ActiveForm::validate($model);
        }
        throw new \yii\web\BadRequestHttpException('Bad request!');
    }
//Для аякс валидации на уникальность email
    public function actionValidateEmail()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $model = new SignupUserForm();
            if($model->load(Yii::$app->request->post()))
                return \yii\widgets\ActiveForm::validate($model);
        }
        throw new \yii\web\BadRequestHttpException('Bad request!');
    }

    public function actionValidateVerifyPhone()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $model = new VerifyPhone();
            if($model->load(Yii::$app->request->post()) )
                return \yii\widgets\ActiveForm::validate($model);
        }
        throw new \yii\web\BadRequestHttpException('Bad request!');
    }
}


