<?php

namespace app\models;

use app\components\CryptBehaviors;
use app\components\DateBehaviors;
use nickcv\encrypter\behaviors\EncryptionBehavior;
use nickcv\encrypter\components\Encrypter;
use yii\behaviors\TimestampBehavior;
use app\components\functions\functions;
use Yii;
use yii\helpers\ArrayHelper;
use app\models\setting\Setting;

/**
 * This is the model class for table "profile".
 *
 * @property integer $id_user
 * @property string $phone
 * @property string $phone2
 * @property string $email
 * @property string $name
 * @property string $surname
 * @property string $patrinimic
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $sex
 * @property string $photo_url
 * @property integer $bithday
 * @property string $status_client
 * @property string $status_vehicle
 * @property string $raiting_client
 * @property string $raiting_vehicle
 * @property string $job_post
 * @property string $power_of_attorney_url
 * @property integer $id_passport
 * @property integer $id_driver_license
 * @property integer $id_reg_license
 * @property string $reg_address
 * @property date $create_at
 * @property string $fioShort
 * @property string $fioFull
 * @property int $rating
 * @property Passports $idPassport
 * @property DriverLicense $driverLicense
 * @property RegLicenses $idRegLicense
 * @property User $idUser
 * @property boolean $is_driver
 * @property string $profileInfo
 * @property string $driverFullInfo
 * @property User $user
 */
class Profile extends \yii\db\ActiveRecord
{
    const STATUS_WAIT_ACTIVATE = 4;

    const STATUS_NOT_CLIENT = 0;
    const STATUS_CLIENT_USER = 1;
    const STATUS_CLIENT = 2;
    const STATUS_VIP_CLIENT = 3;

    const STATUS_NOT_VEHICLE = 0;
    const STATUS_VEHICLE_USER = 1;
    const STATUS_VEHICLE = 2;
    const STATUS_VIP_VEHICLE = 3;

    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';
    const ROLE_CLIENT = 'client';
    const ROLE_VIP_CLIENT = 'vip_client';
    const ROLE_VEHICLE = 'vehicle';
    const ROLE_VIP_VEHICLE = 'vip_vehicle';

    const SCENARIO_SAFE_SAVE = 'safe_save';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'profile';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios(); // TODO: Change the autogenerated stub
        $scenarios[self::SCENARIO_SAFE_SAVE] = [
            'name', 'surname', 'id_user', 'is_driver', 'photo', 'sex'
        ];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_user', 'name', 'surname', 'patrinimic'], 'required'],
            [['id_passport', 'id_driver_license'], 'integer'],
            [['id_user', 'status_client', 'status_vehicle', 'raiting_client', 'raiting_vehicle'],  'integer'],
//            [['phone2'], 'string', 'max' => 32],
            [['name', 'surname', 'patrinimic', 'phone2', 'email2', 'reg_address'], 'string', 'max' => 255],
            [['sex'], 'string', 'max' => 1],
            [['photo'], 'string', 'max' => 255],
//            [['id_passport'], 'exist', 'skipOnError' => true, 'targetClass' => Passport::className(), 'targetAttribute' => ['id_passport' => 'id']],
//            [['id_driver_license'], 'exist', 'skipOnError' => true, 'targetClass' => DriverLicense::className(), 'targetAttribute' => ['id_driver_license' => 'id']],
            [['id_user'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id_user' => 'id']],
            ['photo', 'default', 'value' =>  Setting::getNoPhotoPath()],
            [['bithday'], 'date', 'format' => 'php:d.m.Y'],
            ['is_driver', 'default', 'value' => false],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_user' => Yii::t('app', 'Id'),
            'phone2' => Yii::t('app', 'Дополнительный номер телефона'),
            'email2' => Yii::t('app', 'Дополнительный email'),
            'name' => Yii::t('app', 'Имя'),
            'surname' => Yii::t('app', 'Фамилия'),
            'patrinimic' => Yii::t('app', 'Отчество'),
            'fioFull' => Yii::t('app', 'ФИО'),
            'fioShort' => Yii::t('app', 'ФИО'),
            'sex' => Yii::t('app', 'Пол'),
            'photo' => Yii::t('app', 'Фото пользователя'),
            'bithday' => Yii::t('app', 'Дата рождения'),
            'status_client' => Yii::t('app', 'Статус клиента'),
            'status_vehicle' => Yii::t('app', 'Статус водителя'),
            'raiting_client' => Yii::t('app', 'Рейтинг клиента'),
            'raiting_vehicle' => Yii::t('app', 'Рейтинг водителя'),
            'id_passport' => Yii::t('app', 'Id паспорта'),
            'id_driver_license' => Yii::t('app', 'Id водительского удостоверения'),
            'create_at' => 'Дата создания',
            'reg_address' => 'Адрес регистрации',
        ];
    }

    public function behaviors()
    {
        return [
//            'encryption' => [
//               'class' => '\nickcv\encrypter\behaviors\EncryptionBehavior',
//               'attributes' => [
//                   'surname',
//                   'email2',
//                   'phone2',
//                   'id_passport',
//                   'id_driver_license'
//                ],
//            ],
            'convertDate' => [
                'class' => DateBehaviors::className(),
                'dateAttributes' => ['bithday'],
                'format' => DateBehaviors::FORMAT_DATE,
            ]
        ];
    }



    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPassport()
    {
        return $this->hasOne(Passport::className(), ['id' => 'id_passport']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'id_user']);
    }

    public function getCompanies()
    {
        return $this->hasMany(Company::className(), ['id' => 'id_company'])
            ->viaTable('XprofileXcompany',['id_profile' => 'id_user']);
    }

    /**
     * @inheritdoc
     * @return ProfileQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProfileQuery(get_called_class());
    }
    public function getSex(){
        return $this->sex ? 'Женский' : 'Мужской';
    }
    public function getUrlPhoto(){
        return '/uploads/photos/users/'.$this->photo;
    }

    //Относительный путь фото
    public function getPhotoFullPath(){
        return '/uploads/photos/users/'.$this->photo;
    }

    public function getCountCompanies(){
        return XprofileXcompany::find()->where(['id_profile' => $this->id_user])->count();
    }

    public function getPhone(){
        return $this->user->username;
    }
    public function getEmail(){
        return $this->user->email;
    }

    public function getRoles(){
        $roles = [];
        foreach (Yii::$app->authManager->getAssignments($this->id_user) as $role) {
            $roles[] = $role->roleName;
        }
        return $roles;
    }

    public function getRolesToString(){
        $string = '';
        foreach ($this->getRoles() as $role){
            $string .= $role . ', ';
        }
        $string = substr($string, 0, -2);
        return $string;
    }
    public function getDriverLicense(){
        return $this->hasOne(DriverLicense::class,['id'=>'id_driver_license']);
    }

    public function getMaxCompanies(){
        $countCompsnies = 0;
        $maxCompanies = 0;
        foreach ($this->getRoles() as $role) {
            switch ($role) {
                case self::ROLE_ADMIN:
                    $countCompsnies = 1000;
                    ($countCompsnies > $maxCompanies) ? $maxCompanies = $countCompsnies : false;
                    break;
                case self::ROLE_USER:
                    $countCompsnies = 1;
                    ($countCompsnies > $maxCompanies) ? $maxCompanies = $countCompsnies : false;
                    break;
                case self::ROLE_CLIENT:
                    $countCompsnies = 3;
                    ($countCompsnies > $maxCompanies) ? $maxCompanies = $countCompsnies : false;
                    break;
                case self::ROLE_VIP_CLIENT:
                    $countCompsnies = 5;
                    ($countCompsnies > $maxCompanies) ? $maxCompanies = $countCompsnies : false;
                    break;
                default:
                    $countCompsnies = 0;
                    ($countCompsnies > $maxCompanies) ? $maxCompanies = $countCompsnies : false;
                    break;
            }
        }
        return $maxCompanies;
    }

    public function getFioFull(){
        return $this->surname . ' ' . $this->name . ' ' . $this->patrinimic;
    }

    public function getFioShort(){
        return $this->surname . ' ' . mb_substr($this->name, 0, 1) . '. ' . mb_substr($this->patrinimic, 0, 1) . '. ';
    }
    public function getCreate_at(){
        return date('d.m.Y h:i', $this->user->created_at);
    }
    public function getCompaniesConfirm(){

        $companies = [];
        foreach ($this->companies as $company){
            $modelDocument = \app\models\Document::findOne(['id_company' => $company->id, 'type' => \app\models\Document::TYPE_CONTRACT_CLIENT]);
            $modelPOA = XprofileXcompany::find()->where(['id_profile'=> $this->id_user, 'id_company' => $company->id])->one();
            if($modelDocument->status == Document::STATUS_SIGNED && $modelPOA->STATUS_POA == XprofileXcompany::STATUS_POWER_OF_ATTORNEY_SIGNED){
                $companies[] = $company;
            }

        }
        return $companies;
    }

    public function getRating($type = Review::TYPE_TO_VEHICLE)
    {
        $params = ['id_user_to' => $this->id_user];
        switch ($type) {
            case Review::TYPE_TO_VEHICLE:
                $params[] = ['type' => Review::TYPE_TO_VEHICLE];
                break;
            case Review::TYPE_TO_CLIENT:
                $params[] = ['type' => Review::TYPE_TO_CLIENT];
                break;
        }

        $reviews = Review::findAll($params);
        $totalValue = 0;
        $count = 0;
        if ($reviews) {
            foreach ($reviews as $review) {
                if ($review->value) {
                    $totalValue += $review->value;
                    $count++;
                }
            }
            if ($totalValue) {
                return round($totalValue / $count, 1);
            }
        }
        return false;
    }

    public function getDriverFullInfo($showPhone = false, $showPassport = false, $showDriveLicense = false){
        $return = $this->fioFull . '<br>';
        if($showPhone) {
            $return .= 'Телефон: ' . $this->phone;
            if ($this->phone2) $return .= ' (доп. ' . $this->phone2 . ')';
            $return .= '. <br>';
        }
        if($showPassport) $return .= 'Паспорт: ' . $this->passport->fullInfo . '. <br>';
        if($showDriveLicense) $return .= 'ВУ' . $this->driverLicense->fullInfo . '. <br>';

        return $return;
    }

    public function getProfileInfo($showPhone = false, $showPassport = false, $showEmail = false){
        $return = $this->fioFull . '<br>';
        if($showPhone) {
            $return .=  functions::getHtmlLinkToPhone($this->phone);
            if ($this->phone2) $return .= ' (доп. ' . functions::getHtmlLinkToPhone($this->phone2) . ')';
            $return .= '. <br>';
        }
        if($showPassport) $return .= 'Паспорт: ' . $this->passport->fullInfo . '. <br>';
        if($showEmail) $return .= 'Email: ' . $this->email . ' (' . $this->email2 . ') <br>';

        return $return;
    }

    static public function getArrayPhonesFIO():array {
        $return = [];
        $profiles = self::find()->all();
        foreach ($profiles as $profile){
            $return[] = $profile->phone . ' ' . $profile->fioFull . ' (ID ' . $profile->id_user . ', тел.1)';

            if($profile->phone2) {
                $return[] = $profile->phone2 . ' ' . $profile->fioFull . ' (ID ' . $profile->id_user . ', тел.2)';
            }
        }
        return $return;
    }

    public function hasPOAofCompany($id_company){
        return (XprofileXcompany::find()
            ->where(['id_company' => $id_company])
            ->andWhere(['id_profile' => $this->id_user])
            ->one()->STATUS_POA == XprofileXcompany::STATUS_POWER_OF_ATTORNEY_SIGNED);
    }

    static public function getArrayForAutoComplete($search = false){
        $return = [];
        foreach (self::find()->all() as $profile){
            $return[] = [
                'id' => $profile->id_user,
                'phone' => $profile->phone,
                'phone2' => $profile->phone2,
                'email' => $profile->email,
                'email2' => $profile->email2,
                'name' => $profile->name,
                'surname' => $profile->surname,
                'patrinimic' => $profile->patrinimic,
                'value' => ($search)
                    ?$profile->id_user
                    :$profile->phone . ' (' . $profile->phone2 . ') ' . $profile->fioFull . ' (ID ' . $profile->id_user . ')',
                'label' => $profile->phone . ' (' . $profile->phone2 . ') ' . $profile->fioFull . ' (ID ' . $profile->id_user . ')',
                'companies' => ArrayHelper::map($profile->companies, 'id', 'name'),
                'info' => $profile->profileInfo . ' ' . $profile->getRating()
            ];
        }

        return $return;
    }
}

