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
use app\models\ResetPasswordSmsForm;
use app\models\settings\SettingSMS;
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
use app\models\PasswordResetRequestForm;
use app\models\ResetPasswordForm;

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
            $user = Yii::$app->user;
            if($user->can('admin')){
                return $this->redirect('/logist');
            }
            if($user->can('dispetcher')){
                return $this->redirect('/logist');
            }
            if($user->can('finance')){
                return $this->redirect('/finance');
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
        return $this->redirect('/');

        $modelUser = new User();
        $modelProfile = new Profile();
        $modelVerifyPhone = new VerifyPhone();
        $modelSignupUserForm = new SignupUserForm();
        $modelPhoneForm = new SignupPhoneForm();
        $session = Yii::$app->session;
        if($session->get('modelProfile')) $modelProfile = $session->get('modelProfile');

        switch (Yii::$app->request->post('button')){
            case 'signup1':
                $session->remove('modelUser');
                if($modelProfile->load(Yii::$app->request->post()) && $modelProfile->validate()){
                    $session->remove('modelVerifyPhone');
                    $session->set('modelUser', $modelUser);
//                    $session->set('modelProfile', $modelProfile);
                    $_SESSION['modelProfile'] = $modelProfile;
                    return $this->render('signup2', compact([
//                        'modelVerifyPhone',
                            'modelProfile',
                            'modelUser',
//                            'modelPhoneForm'
                        ])
                    );
                }
                break;
            case 'signup2':
                if(!$session->has('modelProfile') || !$session->has('modelUser')) break;
                if($session->get('modelUser')) $modelUser = $session->get('modelUser');

                $session->remove('modelVerifyPhone');

                if ($modelUser->load(Yii::$app->request->post())) {

                    if($session->get('timeout_new_code') > time()) {
//                        return 1 . $session->get('timeout_new_code');
                        functions::setFlashWarning('Повторная отправка смс-кода возможна через 5 минут после последней попытки');
                        return $this->render('signup2', compact(['modelVerifyPhone', 'modelProfile', 'modelUser']));
                    }
                    $modelVerifyPhone->generateCode();
                    $sms = new Sms($modelUser->username, $modelVerifyPhone->getVerifyCode());
//                     Отправка кода
                    if (!$sms->sendAndSave()) {
                        functions::setFlashWarning('Ошибка на сервере. Попробуйте позже.');
                        break;
                    }
                    $session->set('timeout_new_code', time()+300);

                    $session->set('modelVerifyPhone', $modelVerifyPhone);
                    $session->set('modelUser', $modelUser);
                    return $this->render('signup3', compact(['modelVerifyPhone', 'modelProfile', 'modelUser']));
                }
                return $this->render('signup2', compact([
                    'modelVerifyPhone', 'modelProfile', 'modelUser']));
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
                                emails::sendAfterUserRegistration($modelProfile->id_user);
//                                emails::sendAfterUserRegistration(73);
//                                $session->removeAll();
                                functions::setFlashSuccess('Поздравляем с успешной регистрацией!');
                                $session->remove('modelUser');
                                $session->remove('modelProfile');
                                $session->remove('modelVerifyKey');
                                $session->remove('modelSignupUserForm');
                                return $this->redirect('/');
                            }
                            functions::setFlashWarning('ОШИБКА НА СЕРВЕРЕ. Попробуйте позже.');

                            return $this->render('signup4',
                                compact(['modelVerifyPhone', 'modelProfile', 'modelUser', 'modelSignupUserForm']));
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

            $model2 = new VerifyPhone();
            if($model2->load(Yii::$app->request->post()))
                return \yii\widgets\ActiveForm::validate($model2);
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

    public function actionValidateResetPasswordForm()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $model = new ResetPasswordSmsForm();
            if($model->load(Yii::$app->request->post()))
                return \yii\widgets\ActiveForm::validate($model);
        }
        throw new \yii\web\BadRequestHttpException('Bad request!');
    }

    public function actionResetPasswordSms(){
        $model = new ResetPasswordSmsForm();

        switch (Yii::$app->request->post('button')){
            case 'send_sms':
                if($model->load(Yii::$app->request->post())){
                    if($User = User::findOne(['username' => $model->phone])){
                        if($User->send_last_sms_time + 60*10 < time()){
                            $model->generate_code();
                            if(SettingSMS::find()->one()->sms_code_reset_password) {
                                $sms = new Sms($User->username, $model->get_sms_code());
                                if (!$sms->sendAndSave()) {
                                    functions::setFlashWarning('Ошибка на сервере. Попробуйте позже.');
                                    break;
                                }
                            }else {
                                functions::setFlashWarning('Отправка смс временно не доступна. Воспользуйтесь восстановлением пароля по email');
                                break;
                            }
                            $User->sms_code_for_reset_password = $model->get_sms_code();
                            $User->send_last_sms_time = time();
                            $User->save(false);
                            return $this->render('/default/change-password', ['model' => $model]);
                            break;
                        } else {
                            $model->set_sms_code($User->sms_code_for_reset_password);
                            functions::setFlashWarning('Повторный СМС код Вы сможете запросить в '. date('H:i', $User->send_last_sms_time + 60*10));
                            break;
                        }
                    }
                    functions::setFlashWarning('Пользователь с таким номером телефона не зарегистрирован');
                }
                break;
            case 'change-password':
                if($model->load(Yii::$app->request->post())){
                    if($User = User::findOne(['username' => $model->phone])) {
                        if(!$User->sms_code_for_reset_password){
                            functions::setFlashWarning('Получите СМС код');
                            break;
                        }
                        $model->set_sms_code($User->sms_code_for_reset_password);
                        return $this->render('/default/change-password', ['model' => $model]);
                    }
                    functions::setFlashWarning('Пользователь с таким номером телефона не зарегистрирован');
                }
                break;
            case 'confirm-password':
                if($model->load(Yii::$app->request->post())){
//                    return var_dump($model);
                    if($User = User::findOne(['username' => $model->phone])) {
                        $model->set_sms_code($User->sms_code_for_reset_password);
                        if(!$model->validSmsCode()){
                            functions::setFlashWarning('Код неверный!');
                            return $this->render('/default/change-password', ['model' => $model]);
                        }
                        $User->setPassword($model->password);
                        if($User->save(false)){
                            $User->sms_code_for_reset_password = null;
                            $User->save(false);
                            functions::setFlashSuccess('Пароль успешно изменен!');
                            return $this->redirect('/default/login');
                        }
                        functions::setFlashWarning('Ошибка. Попробуйте позже.');
                    }
                    functions::setFlashWarning('Пользователь с таким номером телефона не зарегистрирован');
                }
                break;
        }

        return $this->render('reset-password-sms', [
            'model' => $model
        ]);
    }

    public function actionResetPasswordEmail(){
        $model = new PasswordResetRequestForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                functions::setFlashSuccess('Письмо с ссылкой на изменение пароля отправлено на Вашу электронную почту.');
                return $this->redirect('/default/login');
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка на сервере. Попробуйте позже.');
            }
        }

        return $this->render('reset-password-email', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            functions::setFlashSuccess('Пароль успешно изменен!');
            return $this->redirect('/default/login');
        }

        return $this->render('resetPasswordForm', [
            'model' => $model,
        ]);
      }

      public function actionUserAgreement(){
        return Yii::$app->response->sendFile(Yii::getAlias('@app'). '/web/documents/user_agreement.docx');

      }
    public function actionPolicy(){
        return Yii::$app->response->sendFile(Yii::getAlias('@app'). '/web/documents/policy.docx');

    }

    public function actionDriverInstruction(){
        return Yii::$app->response->sendFile(Yii::getAlias('@app'). '/web/documents/driver_instruction.docx');

    }

    public function actionContacts(){
        return $this->render('/default/contacts');
    }

    public function actionAbout(){
        return $this->render('/default/about');
    }

}


