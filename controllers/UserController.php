<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 11.01.2018
 * Time: 17:29
 */

namespace app\controllers;

use app\components\functions\emails;
use app\models\ChangePasswordForm;
use app\models\Company;
use app\models\OrderOLD;
use app\models\Passport;
use app\models\Profile;
use app\models\settings\SettingSMS;
use app\models\Sms;
use app\models\UpdateUserProfileForm;
use app\models\User;
use app\models\VerifyPhone;
use phpDocumentor\Reflection\Types\Null_;
use Yii;
use app\components\functions\functions;
use app\models\signUpClient\SignUpClientFormStart;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\HttpException;
use yii\web\UploadedFile;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

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

    public function actionIndex($redirect = '/user')
    {
        $modelUser = User::findOne(Yii::$app->user->identity->getId());
        $modelProfile = $modelUser->profile;
        $UpdateUserProfileForm = new UpdateUserProfileForm();
        $OldProfileAttr = new UpdateUserProfileForm();
        $OldProfileAttr->setAttr($modelProfile);
        $UpdateUserProfileForm->setAttr($modelProfile);

        if ($UpdateUserProfileForm->load(Yii::$app->request->post())) {
            if (!$UpdateUserProfileForm->passport_number) $UpdateUserProfileForm->country = null;
            $UpdateUserProfileForm->photo = UploadedFile::getInstance($UpdateUserProfileForm, 'photo');
            $new_attr = array_diff($UpdateUserProfileForm->attributes, $OldProfileAttr->attributes);
//            return var_dump($UpdateUserProfileForm->photo);
            if ($new_attr || !$UpdateUserProfileForm->photo) {
                $UpdateUserProfileForm->sendToCheck($modelProfile);
                functions::setFlashSuccess('Изменения отправлены на модерацию.');
            } else {
                functions::setFlashWarning('Ничего не изменилось.');
            }
//            return $this->redirect($redirect);
        }
        return $this->render('index', [
            'modelUser' => $modelUser,
            'modelProfile' => $modelProfile,
            'UpdateUserProfileForm' => $UpdateUserProfileForm
        ]);
    }

    public function actionSignupClient()
    {
        $user = Yii::$app->user->identity;
        if(!$user) return $this->redirect('/default/login');
        $modelStart = new SignUpClientFormStart();
        $modelStart->email = $user->email;
        $modelStart->id_user = $user->id;
        if ($modelStart->load(Yii::$app->request->post())) {
            if ($modelProfile = $modelStart->saveProfile()) {
//                return $this->render('signupClient2', compact(['modelProfile']));
                functions::setFlashSuccess('Спасибо, ' . $modelProfile->name . ' ' . $modelProfile->patrinimic
                    . '! Надеемся на долгосрочное сотрудничество!');
                emails::sendAfterClientRegistration($modelProfile->id_user);
                return $this->redirect('/order/client');
            } else {
                functions::setFlashWarning('Ошибка на сервере. Попробуйте позже...');
                return $this->render('signupClientStart', compact(['modelStart', 'modelProfile', 'modelPassport']));
            }
        } else {
            return $this->render('signupClientStart', compact(['modelStart']));
        }
    }

    public function actionValidateUser(){
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $model3 = new User();
//            $model3->scenario = User::SCENARIO_CHANGE_PASS;
            if ($model3->load(Yii::$app->request->post()))
                return \yii\widgets\ActiveForm::validate($model3);
        }
    }

    public function actionValidatePassport()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;


            $model2 = new UpdateUserProfileForm();
            if ($model2->load(Yii::$app->request->post()))
                return \yii\widgets\ActiveForm::validate($model2);

            $model3 = new User();
            if ($model3->load(Yii::$app->request->post()))
                return \yii\widgets\ActiveForm::validate($model3);

            $model = new SignUpClientFormStart();
            if($model->load(Yii::$app->request->post()))
                return \yii\widgets\ActiveForm::validate($model);
        }
        throw new \yii\web\BadRequestHttpException('Bad request!');
    }

    public function actionAddpushallid($user_id = null)
    {

        $pushalluserid = Yii::$app->request->get('pushalluserid');
//        return var_dump(Yii::$app->request->get());
        if ($pushalluserid) {
            if (!$user_id) {
                $user = User::findOne(Yii::$app->user->id);
            } else {
                $user = User::find()->where(['id' => $user_id])->one();
            }
            if (!$user) {
                functions::setFlashWarning('Ошибка на сервере.');
                return $this->redirect('/user');
            }
            if (!is_array($user->push_ids)) {
                $pids = [];
                $pids [] = $pushalluserid;
            } else {
                $hasId = 0;
                foreach ($user->push_ids as $push_id) {
                    if ($push_id == $pushalluserid) $hasId = 1;
                }
                $pids = $user->push_ids;
                if (!$hasId) $pids [] = $pushalluserid;
            }
            $user->push_ids = $pids;
            $user->scenario = $user::SCENARIO_SAVE;
            if ($user->save()) {
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
        $this->layout = '@app/views/layouts/logist';
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
                $tmpProfile->sex = $profile->sex;
                $tmpProfile->is_driver = $profile->is_driver;
                $tmpProfile->phone2 = $profile->phone2;
                $tmpProfile->email2 = $profile->email2;

                $user = $tmpUser;
                $profile = $tmpProfile;
                $user->scenario = $user::SCENARIO_SAVE_WITHOUT_USERNAME;
                $profile->scenario = $profile::SCENARIO_SAFE_SAVE;

                if ($user->save() && $profile->save()) {
                    functions::setFlashSuccess('Пользовватель сохранен.');
                    $this->redirect([$redirect, 'id_user' => $user->id, 'redirect' => $redirect2]);
                } else {
                    functions::setFlashWarning('Ошибка при сохранении пользователя');
                }
            } else {
                if(!$user->email) $user->email=null;
                if($user->email && User::findOne(['email' => $user->email])){
                    functions::setFlashWarning('Пользователь с таким email уже существует');
                    return $this->render('find-user',[
                        'user' => $user,
                        'profile' => $profile
                    ]);
                }
//                $user->setPassword(rand(10000000, 99999999));
                $user->setPassword('123456');
                $user->generateAuthKey();

                $user->scenario = $user::SCENARIO_SAVE;
                $user->status = User::STATUS_WAIT_ACTIVATE;
                $profile->scenario = $profile::SCENARIO_SAFE_SAVE;
                if ($user->save()) {
                    $profile->id_user = $user->id;
                    if($profile->save()){
                        emails::sendAfterUserRegistration($user->id);
                        functions::setFlashSuccess('Пользовватель добавлен.');
                        return $this->redirect([$redirect,
                            'id_user' => $user->id,
                            'id_order' => $id_order,
                            'redirect' => $redirect2
                        ]);
                    }else {
                        functions::setFlashWarning('Ошибка при сохранении пользователя');
                    }
                }else {
                    functions::setFlashWarning('Ошибка при сохранении пользователя');
                }
            }
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

    public function actionChangePhone(){
        $session = Yii::$app->session;
        $User = Yii::$app->user->identity;
        $Profile = $User->profile;
        if($Profile){
            $history_updates = $Profile->history_updates;
            $history_updates[] = [
                time() => ['user' => $User->attributes]
            ];
            $Profile->history_updates = $history_updates;
        }
        if(!$User) throw new HttpException(404, 'Нет такого пользователя');
        $User->username = '';
        $User->scenario = User::SCENARIO_SAVE;
        $VerifyPhone = new VerifyPhone();
        $timer = 0;
        if($session->has('timer')){
            $timer = $session->get('timer') - time();
        }
        if($timer < 0 ) $timer = 0;
        else {
            if($session->get('VerifyPhone')) {
                $VerifyPhone = $session->get('VerifyPhone');
                $VerifyPhone->userCode = '';
            }
        }
        if(Yii::$app->request->isPjax) {
            $User->username = (Yii::$app->request->post('username'));

            if($User->validate()) {
//            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                $User->new_username = $User->username;
                $timer = Yii::$app->request->post('timer');
                if ($timer <= 0) {
                    $VerifyPhone->generateCode();
                    if(SettingSMS::find()->one()->sms_code_update_phone){
                        $sms = new Sms($User->new_username, $VerifyPhone->getVerifyCode());
                        if(!$sms->sendAndSave()){
                            functions::setFlashWarning('Ошибка на сервере. Попробуйте позже.');
                            return $this->redirect('/user');
                        }
                    }
                    $session->set('timer', time() + 300);
                    $timer = $session->get('timer') - time();
                    $session->set('VerifyPhone', $VerifyPhone);
                }
            }
        }

        if($VerifyPhone->load(Yii::$app->request->post())
            && $User->load(Yii::$app->request->post()))
        {
            $User->username = $User->new_username;

            if($User->save()){
                if($Profile) {
                    $Profile->save(false);
                }
                $session->remove('VerifyPhone');
                $User->new_username = '';
                functions::setFlashSuccess('Номер успешно изменен.');
            } else {
                functions::setFlashWarning('Номер не изменен. Попробуйте позже');
            }
            return $this->redirect('/user');
        }
        return $this->render('change-phone',[
            'User' => $User,
            'VerifyPhone' => $VerifyPhone,
            'timer' => $timer
        ]);
    }

    public function actionChangePassword(){
        $ChangePasswordForm = new ChangePasswordForm();

        if($ChangePasswordForm->load(Yii::$app->request->post()) && $ChangePasswordForm->validate()){
            $user = Yii::$app->user->identity;
            if(!$user) throw new HttpException(404, 'ERROR!!!');
            $user->setPassword($ChangePasswordForm->new_pass);
            if($user->save(false)){
                functions::setFlashSuccess('Пароль успешно изменен!');
                return $this->redirect('/user');
            }
            functions::setFlashWarning('Пароль не изменен! Попробуйте еще раз или обратитесь к администратору.');
        }

        return $this->render('change-password', [
            'ChangePasswordForm' => $ChangePasswordForm
        ]);
    }

    public function actionBalance($id_user = null){
        if(!$id_user) $id_user = Yii::$app->user->id;

        $Profile = Profile::findOne($id_user);
        if(!$Profile) throw new HttpException(404, 'Страница не найдена');
        $User = $Profile->user;
        $Balance = $Profile->balance;
        $dataProvider_car_owner = [];
        $dataProvider_user = [];
        $dataProviders_companies = [];

        if($User->canRole('car_owner')){
            $dataProvider_car_owner = new ArrayDataProvider([
                'allModels' => $Balance['balance_car_owner']['orders'],
                'pagination' => ['pageSize' => 15],
                'sort' => [
                    'attributes' => ['date'],
                    'defaultOrder' => [
                        'date' => SORT_DESC
                    ]
                ]
            ]);
        }
        if($User->canRole('client') || $User->canRole('car_owner')) {
            $balance = [
                'car_owner' => 0,
                'not_paid' => 0,
                'user' => $Balance['balance_user']['balance'],
                'companies' => $Balance['balance_companies']['balance']
            ];
            $dataProviders_companies = [];
            $ids_companies = '';
            foreach ($Balance['balance_companies'] as $id_company => $orders){
                if($company = Company::findOne($id_company)){
                    $dataProviders_companies[$id_company] = new ArrayDataProvider([
                        'allModels' => $Balance['balance_companies'][$id_company]['orders'],
                        'pagination' => ['pageSize' => 15],
                        'sort' => [
                            'attributes' => ['date'],
                            'defaultOrder' => [
                                'date' => SORT_DESC
                            ]
                        ]
                    ]);
                    $ids_companies .= $id_company . ' ';
                }
            }
            $ids_companies = substr($ids_companies, 0, -1);
        }
        if($User->canRole('user')) {
            $balance = [
                'car_owner' => 0,
                'not_paid' => 0,
                'user' => $Balance['balance_user']['balance'],
                'companies' => 0
            ];
        }

        $dataProvider_user = new ArrayDataProvider([
            'allModels' => $Balance['balance_user']['orders'],
            'pagination' => ['pageSize' => 15],
            'sort' => [
                'attributes' => ['date'],
                'defaultOrder' => [
                    'date' => SORT_DESC
                ]
            ]
        ]);

        return $this->render('balance', [
            'dataProvider_car_owner' => $dataProvider_car_owner,
            'dataProvider_user' => $dataProvider_user,
            'dataProviders_companies' => $dataProviders_companies,
            'balance' => $balance,
            'Balance' => $Balance,
            'ids_companies' => $ids_companies
        ]);

    }
}