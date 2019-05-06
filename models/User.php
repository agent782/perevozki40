<?php

namespace app\models;

use app\components\DateBehaviors;
use app\components\SerializeBehaviors;
use app\components\UserBehaviors;
use wowkaster\serializeAttributes\SerializeAttributesBehavior;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\data\SqlDataProvider;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

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
 * @property array $drivers
 * @property array $vehicles
 * @property array $vehicleIds
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    const SCENARIO_SAVE = 'save';

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
            'status'
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
                'arrAttributes' => ['push_ids']
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'unique', 'message' => 'Пользователь с таким номером телефона уже зарегистрирован'],
            ['username', 'required', 'message' => 'Введите номер телефона', 'message' => 'Введите Ваш номер телефона'],
            ['email', 'email'],
//            ['username', 'match', 'pattern' => '/^\+7\([0-9]{3}\)[0-9]{3}\-[0-9]{2}\-[0-9]{2}$/'],
            ['username', 'validateLengthPhone'],
//                'message' => 'Неверный формат номера','tooLong' => 'Неверный формат номера','tooShort' => 'Неверный формат номера'
//            ],
//            ['email', 'safe'],
            ['created_at', 'default', 'value' => date('d.m.Y h:i')],
            ['updated_at', 'default', 'value' => date('d.m.Y h:i')],
            ['active_at', 'default', 'value' => date('d.m.Y h:i')],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            ['captcha' , 'captcha', 'message' => 'Введите код, как на картинке.'],
            [['push_ids'], 'safe']
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
            'captcha' => 'Проверочный код'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
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
//        return Profile::findOne($this->getId());
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
            'status' => self::STATUS_ACTIVE,
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
            ->where([user_id => $this->id])
            ->one()['item_name'];

    }
    public function getRoles(){
        return Yii::$app->authManager->getRolesByUser($this->getId());
    }

        public function canRole(string $roleName){
        $userRoles = Yii::$app->authManager->getRolesByUser($this->id);
        foreach ($userRoles as $userRole){
            if($userRole->name == $roleName ) return true;
        }
        return false;
    }
//
    public function delete()
    {
        $user = User::findOne($this->getId());
        $user -> status = self::STATUS_DELETED;
        $auth = Yii::$app->authManager;
        if($user->save()) {
            $role = $auth->getRole('deleted');
            $auth->revokeAll($this->getId());
            $auth->assign($role,$this->getId());
            return true;
        }
        return false;
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





}