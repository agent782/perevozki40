<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 11.12.2017
 * Time: 12:57
 */

namespace app\controllers;
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

    public function actionTest(){

//        (Yii::$app->mailer->compose('test', [
//            'profile' => \app\models\Profile::findOne(['id_user' => Yii::$app->user->id]),
//        ])
//            ->setFrom('admin@perevozki40.ru')
//            ->setTo('agent782@yandex.ru')
//            ->setSubject('Subject')
////            ->setTextBody('TEAT TEAT TEAT')
//        ->send())
//        ?
//        Yii::$app->session->setFlash('success', 'Письмо отправлено'):
//            Yii::$app->session->setFlash('warning', 'Письмо  НЕ ОТПРАВЛЕНО!!');
//        return $this->redirect('/');

//        $to = "9206167111";
//        $message = "Test\nTest\nTest\nTest";
//        return var_dump(Yii::$app->smsru->send($to, $message));
//      echo
        var_dump((new Sms('9206167111', 'Проверка'))->sendAndSave());
//            var_dump($sms->sendAndSave());
//                echo var_dump($sms->checkBalance());
//        var_dump (Yii::$app->smsru->status('error_1539331510_3850')->status_code);

//        foreach (Yii::$app->smsru->status('201841-1000009')->sms as $sms){
//            echo $data->status_text;
//            echo($sms->status_text);
//            echo '<br>';
//        }
//        var_dump(Sms::find()->select('id')->asArray()->all());
        echo Sms::updateStatuses() . '<br>' . Sms::checkBalance();
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
                    $modelVerifyPhone->generateCode();
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
                                return $this->redirect('/user');
                            }
                            functions::setFlashWarning('ОШИБКА НА СЕРВЕРЕ. Попробуйте позже.');

                            return $this->render('signup4', compact(['modelVerifyPhone', 'modelProfile', 'modelUser', 'modelSignupUserForm']));
                        }
                    }
                    functions::setFlashWarning('ОШИБКА НА СЕРВЕРЕ. Попробуйте позже.');
                    $session->remove('modelUser');
                    $session->remove('modelProfile');
                    $session->remove('modelVerifyKey');
                    $session->remove('modelSignupUserForm');
                    return $this->redirect('/default/signup');
                    return var_dump($modelUser->getErrors());

//                        'ОШИБКА НА СЕРВЕРЕ. Попробуйте позже. <a href = "/">На главную</a>';
//                    return $this->render('signup4', compact(['modelProfile', 'modelUser']));
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


