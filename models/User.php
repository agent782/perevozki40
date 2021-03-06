<?php

namespace app\models;

use app\components\DateBehaviors;
use app\components\functions\emails;
use app\components\SerializeBehaviors;
use app\components\UserBehaviors;
use app\models\settings\SettingProfile;
use function Sodium\compare;
use wowkaster\serializeAttributes\SerializeAttributesBehavior;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\data\SqlDataProvider;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\IdentityInterface;
use app\models\Profile;
use yii\data\ArrayDataProvider;
use yii\db\Expression;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 * @property array $push_ids
 * @property array $firebase_ids
 * @property array $drivers
 * @property array $vehicles
 * @property array $vehicleIds
 * @property Profile $profile
 * @property string $sms_code_for_reset_password
 * @property integer $send_last_sms_time
 * @property string $old_id
 * @property SettingProfile $settingProfile
 *
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_WAIT_ACTIVATE = 1;
    const STATUS_AUTO_REGISTRATION = 2;
    const STATUS_ACTIVE = 10;

    const SCENARIO_SAVE = 'save';
    const SCENARIO_SAVE_WITHOUT_USERNAME = 'save_without_username';
    const SCENARIO_CHANGE_PASS = 'change_pass';


    public $new_username;
    public $captcha;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }


    public function scenarios()
    {
        $scenarios =  parent::scenarios(); // TODO: Change the autogenerated stub
        $scenarios[self::SCENARIO_SAVE] = ['username',
            'email',
            'created_at',
            'updated_at',
            'active_at',
            'status',
            'new_username',
            'old_id',
		'firebase_ids'
        ];
        $scenarios[self::SCENARIO_CHANGE_PASS] = [
            'old_pass', 'new_pass','new_pass_repeat', 'old_id'
        ];
        $scenarios[self::SCENARIO_SAVE_WITHOUT_USERNAME] = [
            'email', 'update_at', 'old_id'
        ];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
//            TimestampBehavior::className(),
            'addUserRole' => [
                'class' => UserBehaviors::className(),
//                '_id' => Yii::$app->user->getId(),
            ],
//            'encryption' => [
//                'class' => '\nickcv\encrypter\behaviors\EncryptionBehavior',Da
//                'attributes' => [
//                    'username',
//                    'email'
//                ],
//            ],
            'convertDate' => [
                'class' => DateBehaviors::className(),
                'dateAttributes' => [
                    'created_at',
                    'updated_at',
                    'active_at'
                ],
                'format' => DateBehaviors::FORMAT_DATETIME,
            ],
            'SerializeUnserialize' => [
                'class' => SerializeBehaviors::class,
                'arrAttributes' => ['push_ids', 'firebase_ids']
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'unique', 'skipOnError'  => false , 'skipOnEmpty' => false,
                'message' => 'Пользователь с таким номером телефона уже зарегистрирован. 
                Если Вы уже являетесь нашим Клиентом, возможно, Вы были зарегистрированы автоматически.'
                    . Html::a('Восстановить пароль', '/default/login')
            ],
            ['username', 'required', 'skipOnError'  => false , 'skipOnEmpty' => false, 'message' => 'Введите Ваш номер телефона'],
            ['email', 'email'],
//            ['username', 'match', 'pattern' => '/^\+7\([0-9]{3}\)[0-9]{3}\-[0-9]{2}\-[0-9]{2}$/'],
            ['username', 'validateLengthPhone', 'skipOnError'  => false , 'skipOnEmpty' => false],
//                'message' => 'Неверный формат номера','tooLong' => 'Неверный формат номера','tooShort' => 'Неверный формат номера'
//            ],
//            ['email', 'safe'],
            ['created_at', 'default', 'value' => date('d.m.Y h:i')],
            ['updated_at', 'default', 'value' => date('d.m.Y h:i')],
            ['active_at', 'default', 'value' => date('d.m.Y h:i')],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
//            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            ['captcha' , 'captcha', 'message' => 'Введите код, как на картинке.'],
            [['push_ids', 'firebase_ids', 'new_username', 'send_last_sms_time'], 'safe'],
            ['sms_code_for_reset_password', 'string', 'max' => 10],
            ['old_id', 'string' , 'max' => 31]
        ];
    }


    public function validateLengthPhone(){
            $this->username = mb_ereg_replace("[^0-9]",'',$this->username);
            $len = mb_strlen($this->username);
            if($len != 10 && $len != 11){
                $this->addError('username', 'Неверный формат номера');
            }
    }




//    public function beforeValidate()
//    {
//       return $this->username = mb_ereg_replace("[^0-9]",'',$this->username);
//    }
    public function attributeLabels()
    {
        return [
            'username' => 'Номер телефона',
            'email' => 'Адрес электронной почты',
            'captcha' => 'Проверочный код',
            'old_id' => 'Предыдущее условное обозначение'
        ];
    }
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if($insert){
            $setting = new SettingProfile();
            $setting->id_user = $this->id;
            $setting->save();
        }
        emails::sendToAdminAfterSaveUser($this->id);
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }

    public function beforeDelete()
    {
        if($this->profile) {
            $this->profile->unlinkAll('companies', true);
//                    Yii::$app->db->createCommand()->truncateTable('passports')->execute();
            if($this->profile->passport){
                $this->profile->passport->delete();
            }
            if($this->profile->driverLicense){
                $this->profile->driverLicense->delete();
            }
            if($this->drivers){
                foreach ($this->drivers as $driver) {
                    if($driver->passport) {
                        $driver->passport->delete();
                    }
                    if($driver->license) {
                        $driver->license->delete();
                    }
                    $driver->delete();
                }
            }

            if($this->vehicles){
                foreach ($this->vehicles as $vehicle){
                    $vehicle->unlinkAll('loadingtypes', true);
                    $vehicle->unlinkAll('price_zones', true);
                    if($vehicle->regLicense){
                        $vehicle->regLicense->delete();
                    }

                    $vehicle->delete();
                }
            }

//                    $user->profile->delete();
        }
        if($this->profile) {
            $this->unlink('profile', $this->profile, true);
        }
        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token]);
//        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' =>
            ($username),
//            'status' => self::STATUS_ACTIVE
//
            ]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['id_user' => 'id']);
    }

    public function getDateCreatedAt()
    {
        return date('d.m.Y h:m', $this->created_at);
    }

    public function getDateUpdatedAt()
    {
        return date('d.m.Y h:m', $this->updated_at);
    }
//Восстановление пароля
    public static function findByPasswordResetToken($token)
    {

        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
//            'status' => self::STATUS_ACTIVE,
        ]);
    }

    public static function isPasswordResetTokenValid($token)
    {

        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function getRole(){
       return $role = (new Query())
            ->select('item_name')
            ->from('auth_assignment')
            ->where(['user_id' => $this->id])
            ->one()['item_name'];

    }

    public function getRoles($array = false){
        if(!$array) {
            return Yii::$app->authManager->getRolesByUser($this->getId());
        } else {
            $return = [];
            foreach (Yii::$app->authManager->getRolesByUser($this->getId()) as $role){
                $return[$role->name] = $role->name;
            }
            return $return;
        }
    }

    public function canRole(string $roleName){
        $userRoles = Yii::$app->authManager->getRolesByUser($this->id);
        foreach ($userRoles as $userRole){
            if($userRole->name == $roleName ) return true;
        }
        return false;
    }
//
//    public function delete()
//    {
//        $user = User::findOne($this->getId());
//        $user -> status = self::STATUS_DELETED;
//        $auth = Yii::$app->authManager;
////        if($user->save()) {
////            $role = $auth->getRole('deleted');
//        $auth->revokeAll($this->getId());
////            $auth->assign($role,$this->getId());
////            return true;
////        }
//        return false;
//    }
    public function addRole(string $newRole){
        if($this->canRole($newRole)) return false;

    }

    public function getDrivers(){
        return $this->hasMany(Driver::class, ['id_car_owner' => 'id']);
    }

    public function getVehicles(){
        return $this->hasMany(Vehicle::class, ['id_user' => 'id']);
    }

    public  function statusDeleted(){
        return $this->status === self::STATUS_DELETED ? true : false;
    }

    public function getVehicleIds(){
        $arr = [];
        foreach ($this->vehicles as $vehicle){
            $arr [] = $vehicle->id;
        }
        return $arr;
    }

    public function getRequestPayments(){
        return $this->hasMany(RequestPayment::class, ['id_user' => 'id']);
    }

    public function getSettingProfile(){
        return $this->hasOne(SettingProfile::class, ['id_user' => 'id']);
    }

    public function getRolesString(){
        $return = '';
        $roles = $this->roles;
        if($roles) {
            foreach ($roles as $role) {
                $return .= $role->name . ' ';
            }
        }
        return $return;
    }

    static public function arrayBalanceParamsForRender($id_user = null){
        if(!$id_user) $id_user = Yii::$app->user->id;
        $Profile = Profile::findOne($id_user);
//        return var_dump($Profile);
        if(!$Profile) throw new HttpException(404, 'Страница не найдена');
        $User = $Profile->user;
        $balance = [];
        $Balance = $Profile->balance;
        $dataProvider_car_owner = [];
        $dataProvider_user = [];
        $dataProviders_companies = [];
        $ids_companies = '';

        if($User->canRole('user')|| !Profile::notAdminOrDispetcher()) {
            $balance = [
                'car_owner' => 0,
                'not_paid' => 0,
                'user' => $Balance['balance_user']['balance'],
                'companies' => 0
            ];
        }
        if($User->canRole('client')
            || $User->canRole('vip_client')
            || $User->canRole('car_owner')
            || $User->canRole('vip_car_owner')
            || !Profile::notAdminOrDispetcher()) {
            $balance = [
                'car_owner' => 0,
                'not_paid' => 0,
                'user' => $Balance['balance_user']['balance'],
                'companies' => $Balance['balance_companies']['balance']
            ];
            if($Balance && $Balance['balance_companies']) {
                foreach ($Balance['balance_companies'] as $id_company => $orders) {
                    if ($company = Company::findOne($id_company)) {
                        $dataProviders_companies[$id_company] = new ArrayDataProvider([
                            'allModels' => $Balance['balance_companies'][$id_company]['orders'],
                            'pagination' => ['pageSize' => 15],
                        ]);
                        $ids_companies .= $id_company . ' ';
                    }
                }
                $ids_companies = substr($ids_companies, 0, -1);
            }
        }
        if($User->canRole('car_owner')
            || $User->canRole('vip_car_owner')
            || !Profile::notAdminOrDispetcher()){
            $balance = [
                'car_owner' => $Balance['balance_car_owner']['balance'],
                'not_paid' => $Balance['balance_car_owner']['not_paid'],
                'user' => $Balance['balance_user']['balance'],
                'companies' => $Balance['balance_companies']['balance']
            ];
            $dataProvider_car_owner = new ArrayDataProvider([
                'allModels' => $Balance['balance_car_owner']['orders'],
                'pagination' => ['pageSize' => 15],
            ]);
        }

        $dataProvider_user = new ArrayDataProvider([
            'allModels' => $Balance['balance_user']['orders'],
            'pagination' => ['pageSize' => 15],
        ]);

        return [
            'dataProvider_car_owner' => $dataProvider_car_owner,
            'dataProvider_user' => $dataProvider_user,
            'dataProviders_companies' => $dataProviders_companies,
            'balance' => $balance,
            'Balance' => $Balance,
            'ids_companies' => $ids_companies
        ];
    }





}
