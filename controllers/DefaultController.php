<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 11.12.2017
 * Time: 12:57
 */

namespace app\controllers;
use app\models\LoginForm;
use app\models\Profile;
use app\models\SignupPhoneForm;
use app\models\User;
use app\models\VerifyPhone;
use app\models\SignupUserForm;
use Yii;
use app\models\auth_item;
use yii\web\Controller;

//use yii\web\Controller;

class DefaultController extends Controller
{
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
            return $this->goHome();
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

        return $this->goHome();
    }

    //Регистрация
    public function actionSignup(){
        $modelUser = new User();
        $modelProfile = new Profile();
        $modelVerifyPhone = new VerifyPhone();
        $modelSignupUserForm = new SignupUserForm();
        $modelPhoneForm = new SignupPhoneForm();
        $session = Yii::$app->session;
       //Для аякс валидации на уникальность телефона
//        if (Yii::$app->request->isAjax) {
//            Yii::$app->response->format = Response::FORMAT_JSON;
//            return ActiveForm::validate($modelUser);
//        }
        switch (Yii::$app->request->post('button')){
            case 'signup1':
                if($modelProfile->load(Yii::$app->request->post())){
                    $session->set('modelProfile', $modelProfile);
                    return $this->render('signup2', compact(['modelVerifyPhone', 'modelProfile', 'modelUser', 'modelPhoneForm']));
                }
                break;
            case 'signup2':
                if ($modelUser->load(Yii::$app->request->post())) {
                    $modelVerifyPhone->generateCode();
                    $session->set('modelVerifyPhone', $modelVerifyPhone);
                    $session->set('modelUser', $modelUser);
                    $modelProfile = $session->get('modelProfile');
                    return $this->render('signup3', compact(['modelVerifyPhone', 'modelProfile', 'modelUser']));
                }
                return $this->render('signup2', compact(['modelVerifyPhone', 'modelProfile', 'modelUser']));
                break;
            case 'signup3':
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ActiveForm::validate($modelUser);
                }
                $modelVerifyPhone = $session->get('modelVerifyPhone');
                $modelUser = $session->get('modelUser');
                $modelProfile = $session->get('modelProfile');
                //отправить код по смс

                if($modelVerifyPhone->load(Yii::$app->request->post())){
                    if($modelVerifyPhone->checkUserCode()){

                        return $this->render('signup4', compact(['modelVerifyPhone', 'modelProfile', 'modelUser','modelSignupUserForm']));
                    }
                    else{
                        $session->setFlash('errorCode', 'Код не верный');
                        return $this->render('signup3', compact(['modelVerifyPhone', 'modelProfile', 'modelUser']));
                    }
                }
                break;
            case 'signup4':
                if($modelSignupUserForm->load(Yii::$app->request->post())){
                    $modelUser = $session->get('modelUser');
                    $modelProfile = $session->get('modelProfile');
                    if ($modelUser = $modelSignupUserForm->signup($modelUser)) {
                        if (Yii::$app->getUser()->login($modelUser)) {
                            $modelProfile->id_user = $modelUser->id;
                            if ($modelProfile->save()) {
                                $session->set('modelUser', $modelUser);
                                $session->set('modelProfile', $modelProfile);
                                return $this->redirect('/user');
                            }
                            return $this->render('signupUserFinish', compact(['modelProfile', 'modelUser']));
                        }
                    }
                    return var_dump($modelUser);
//                        'ОШИБКА НА СЕРВЕРЕ. Попробуйте позже. <a href = "/">На главную</a>';
//                    return $this->render('signup4', compact(['modelProfile', 'modelUser']));
                }
                break;
            default:
                break;
        }



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
}


