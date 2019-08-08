<?php

namespace app\models;

use app\components\CryptBehaviors;
use app\components\DateBehaviors;
use app\components\SerializeBehaviors;
use app\components\SerializeAndCryptBehaviors;
use app\models\setting\SettingVehicle;
use nickcv\encrypter\components\Encrypter;
use yii\behaviors\TimestampBehavior;
use app\components\functions\functions;
use Yii;
use yii\helpers\ArrayHelper;
use app\models\setting\Setting;
use yii\bootstrap\Html;

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
 * @property string $photo
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
 * @property string $history_updates
 * @property integer $check_update_status
 * @property string $update_to_check;
 * @property Passport $passport
 * @property integer $procentVehicle
 * @property array $balance
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

    const CHECK_UPDATE_STATUS_WAIT = 2;
    const CHECK_UPDATE_STATUS_YES = 1;
    const CHECK_UPDATE_STATUS_NO = 0;

    const SCENARIO_SAFE_SAVE = 'safe_save';

    public $Update = [
        'user' => [],
        'profile' => [],
        'passport' => []
    ];
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
            'name', 'id_user', 'is_driver', 'photo', 'sex', 'email2', 'phone2'
        ];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_user', 'name', 'surname'], 'required'],
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
            ['is_driver', 'default', 'value' => 0],
            [['history_updates', 'update_to_check'], 'safe'],
            ['check_update_status', 'default', 'value' => self::CHECK_UPDATE_STATUS_WAIT]
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
            ],
            'serialize' => [
                'class' => SerializeBehaviors::class,
                'arrAttributes' => ['update_to_check']
            ],
            'encryption' => [
                'class' => SerializeAndCryptBehaviors::class,
                'attrs' => [
                    'history_updates'
                ],
            ],
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

    public function beforeDelete()
    {
        $this->deletePhoto();
        return parent::beforeDelete(); // TODO: Change the autogenerated stub
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
        return '/uploads/photos/users/' . $this->photo;
    }

    public function getUrlUpdatePhoto(){
        return '/uploads/photos/users/update/' . $this->update_to_check['photo'];
    }

    //Относительный путь фото
    public function getPhotoFullPath(){
        return Yii::getAlias('@userPhotoDir/').$this->photo;
    }

    public function deletePhoto(){
        $file = $this->getPhotoFullPath();
        if(file_exists($file && is_file($file))) {
            if(!unlink($file)) return false;
        }
        $this->photo = Setting::find()->one()->noPhotoPath;
        return true;
    }

    public function getCountCompanies(){
        return XprofileXcompany::find()->where(['id_profile' => $this->id_user])->count();
    }

    public function getPhone(){
        if($this->user) {
            return $this->user->username;
        }
        return false;
    }

    public function getEmail(){
        if($this->user) {
            return $this->user->email;
        }
        return false;
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
        return $this->user->created_at;
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

    public function getDriverFullInfo($showPhone = false, $showPassport = false, $showDriveLicense = false, $html = false){
        $return = $this->fioFull . '<br>';
        if($showPhone) {
            $return .= 'Телефон: ' . functions::getHtmlLinkToPhone($this->phone, $html);
            if ($this->phone2) $return .= ' (доп. ' . functions::getHtmlLinkToPhone($this->phone2, $html) . ')';
            $return .= '. <br>';
        }
        if($showPassport && $this->passport) $return .= 'Паспорт: ' . $this->passport->fullInfo . '. <br>';
        if($showDriveLicense && $this->driverLicense) $return .= 'ВУ: ' . $this->driverLicense->fullInfo . '. <br>';

        return $return;
    }

    public function getProfileInfo($showPhone = false, $showPassport = false, $showEmail = false, $html = false){
        $return = $this->fioFull . '<br>';
        if($showPhone) {
            $return .=  functions::getHtmlLinkToPhone($this->phone, $html);
            if ($this->phone2) $return .= ' (доп. ' . functions::getHtmlLinkToPhone($this->phone2, $html) . ')';
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
                'sex' => $profile->sex,
                'is_driver' => $profile->is_driver,
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

    //Получить публичные атрибуты...то, что профиль может редактировать
    public function getPublicAttributes():array {
        $return =
            [
                'surname' => $this->surname,
                'name' => $this->name,
                'patrinimic' => $this->patrinimic,
                'sex' => $this->sex,
                'bithday' => $this->bithday,
                'email' => $this->email,
                'email2' => $this->email2,
                'phone2' => $this->phone2,
                'reg_address' => $this->reg_address,
            ];

        return $return;
    }

    public function getBalance(){
        if($this->user->canRole('car_owner')) {
            $balance_user = $this->getBalanceClient();
            $balance_companies = $this->getBalanceCompanies();
            $balance_car_owner = $this->getBalanceCarOwner();
            $balance_text = $balance_car_owner['balance'];
            $balance_text .= ' / ' . ($balance_user['balance'] + $balance_companies['balance']);

            return [
                'balance' => $balance_car_owner['balance'] + $balance_user['balance'] + $balance_companies['balance'],
                'balance_text' => $balance_text . Html::icon('rub'),
                'balance_car_owner' => $balance_car_owner,
//                'balance_client' => $balance_user['balance'] + $balance_companies['balance'],
                'balance_user' => $balance_user,
                'balance_companies' => $balance_companies
            ];
        }
        if($this->user->canRole('client')
           || $this->user->canRole('user')) {
            $balance_user = $this->getBalanceClient();
            $balance_companies = $this->getBalanceCompanies();
            $balance = $balance_user['balance'] + $balance_companies['balance'];
            return [
                'balance' => $balance,
                'balance_text' => $balance . Html::icon('rub'),
                'balance_user' => $balance_user,
                'balance_companies' => $balance_companies
            ];
        }
    }

    public function getBalanceClient(){
        $return = [
            'balance' => 0,
            'orders' => [],
            'payments' => [],
            'not_paid' => 0,
            'orders_not_paid' => [],
            'orders_avans' => [],
        ];

        $orders_cash = Order::find()->where(['id_user' => $this->id_user,])
            ->andWhere(['in', 'status', [Order::STATUS_CONFIRMED_VEHICLE, Order::STATUS_CONFIRMED_CLIENT]])
            ->andWhere(['type_payment' => Payment::TYPE_CASH])
            ->all();
        $orders_card = Order::find()->where(['id_user' => $this->id_user,])
            ->andWhere(['in', 'status', [Order::STATUS_CONFIRMED_VEHICLE, Order::STATUS_CONFIRMED_CLIENT]])
            ->andWhere(['type_payment' => Payment::TYPE_SBERBANK_CARD])
            ->all();
        $payments = Payment::find()->where(['id_user' => $this->id_user])
            ->andWhere(['status' => Payment::STATUS_SUCCESS])
            ->andWhere(['calculation_with' => Payment::CALCULATION_WITH_CLIENT])
            ->andWhere(['id_user' => $this->id_user])
            ->andWhere(['<>', 'type', Payment::TYPE_BANK_TRANSFER])
            ->all();

        foreach ($orders_cash as $order){
            if($order->paid_status == $order::PAID_YES){
                $return['orders'][] = [
                    'date' => $order->datetime_finish,
                    'debit' => $order->cost_finish,
                    'credit' => $order->cost_finish,
                    'description' => 'Заказ № ' . $order->id . '. Оплачен водителю.',
                    'id_order' => $order->id,
                ];
            } else {
                $return['balance'] -= $order->cost_finish + $order->avans_client;
                $return['orders'][] = [
                    'date' => $order->datetime_finish,
                    'debit' => $order->cost_finish,
                    'credit' => $order->avans_client,
                    'description' => 'Заказ № ' . $order->id . '. Частично оплачен или не оплачен водителю.',
                    'id_order' => $order->id,
                ];
            }
        }

        foreach ($orders_card as $order){
            $return['balance'] -= $order->cost_finish;

            $return['orders'][] = [
                'date' => $order->datetime_finish,
                'credit' => $order->cost_finish,
                'description' => 'Заказ № ' . $order->id,
                'id_order' => $order->id,
            ];
        }

        foreach ($payments as $payment){
            if($payment->direction == Payment::DEBIT) {
                $return['balance'] += $payment->cost;
            }
            if($payment->direction == Payment::CREDIT) {
                $return['balance'] -= $payment->cost;
            }

            $return['orders'][] = [
                'date' => $payment->date,
                'debit' => $payment->cost,
                'description' => $payment->comments,
                'id_order' => '',
                'id_paiment' => $payment->id
            ];
        }

        array_multisort($return['orders'], SORT_DESC);
        return $return;
    }

    public function getBalanceCompanies(){
        $companies = $this->companies;
        $return = [
            'balance' => 0,
        ];
        foreach ($companies as $company){
            if($this->hasFinishOrderOfCompany($company->id)) {
                $return[$company->id] = [
                    'balance' => 0,
                    'orders' => []
                ];
                foreach ($company->orders as $order) {
                    $return['balance'] -= $order->cost_finish;
                    $return[$company->id]['balance'] -= $order->cost_finish;
                    $return[$company->id]['orders'][] = [
                        'date' => $order->datetime_finish,
                        'credit' => $order->cost_finish,
                        'description' => 'Заказ № ' . $order->id,
                        'id_order' => $order->id,
                    ];
                }
                foreach ($company->payments as $payment) {
                    $return['balance'] += $payment->cost;
                    $return[$company->id]['balance'] += $payment->cost;
                    $return[$company->id]['orders'][] = [
                        'date' => $payment->date,
                        'debit' => $payment->cost,
                        'description' => $payment->comments,
                        'id_paiment' => $payment->id
                    ];
                }
                array_multisort($return[$company->id]['orders'], SORT_DESC);
            }
        }
        return $return;
    }

    public function getBalanceCarOwner(){
        $return = [
            'balance' => 0,
            'orders' => [],
            'not_paid' => 0,
            'orders_not_paid' => [],
            'orders_avans' => [],

            'payments' => [],
        ];

        $orders = Order::find()->where(['id_car_owner' => $this->id_user,])
            ->andWhere(['in', 'status', [Order::STATUS_CONFIRMED_VEHICLE, Order::STATUS_CONFIRMED_CLIENT]])
            ->andWhere(['<>', 'type_payment', Payment::TYPE_CASH])
            ->andWhere(['paid_status' => Order::PAID_YES])
            ->all();

        $orders_not_paid = Order::find()->where(['id_car_owner' => $this->id_user,])
            ->andWhere(['in', 'status', [Order::STATUS_CONFIRMED_VEHICLE, Order::STATUS_CONFIRMED_CLIENT]])
            ->andWhere(['<>', 'type_payment', Payment::TYPE_CASH])
            ->andWhere(['paid_status' => Order::PAID_NO])
            ->all();

        $orders_avans = Order::find()->where(['id_car_owner' => $this->id_user,])
            ->andWhere(['in', 'status', [Order::STATUS_CONFIRMED_VEHICLE, Order::STATUS_CONFIRMED_CLIENT]])
            ->andWhere(['<>', 'type_payment', Payment::TYPE_CASH])
            ->andWhere(['paid_status' => Order::PAID_YES_AVANS])
            ->all();

        $orders_cash = Order::find()->where(['id_car_owner' => $this->id_user,])
            ->andWhere(['in', 'status', [Order::STATUS_CONFIRMED_VEHICLE, Order::STATUS_CONFIRMED_CLIENT]])
            ->andWhere(['type_payment' => Payment::TYPE_CASH])
            ->andWhere(['in', 'paid_status', [Order::PAID_YES], Order::PAID_YES_AVANS])
            ->all();

        $payments = Payment::find()->where(['id_user' => $this->id_user])
            ->andWhere(['status' => Payment::STATUS_SUCCESS])
            ->andWhere(['calculation_with' => Payment::CALCULATION_WITH_CAR_OWNER])
            ->all();

        foreach ($orders as $order){
            $return['balance'] += round($order->cost_finish_vehicle - ($order->cost_finish_vehicle * $this->procentVehicle/100));

            $return['orders'][] = [
                'date' => $order->datetime_finish,
                'credit' => '',
                'debit' => round($order->cost_finish_vehicle - ($order->cost_finish_vehicle * $this->procentVehicle/100)),
                'description' => 'Сумма к выплате за заказ № ' . $order->id,
                'id_order' => $order->id,
            ];

        }

        foreach ($orders_cash as $order){
            $return['orders'][] = [
                'date' => $order->datetime_finish,
                'credit' => round($order->cost_finish_vehicle * $this->procentVehicle/100),
                'debit' => '',
                'description' => 'Проценты за заказ № ' . $order->id,
                'id_order' => $order->id,
            ];

            $return['balance'] -= round($order->cost_finish_vehicle * $this->procentVehicle/100);
        }

        foreach ($orders_not_paid as $order){
            $return['not_paid'] += round($order->cost_finish_vehicle - ($order->cost_finish_vehicle * $this->procentVehicle/100));
            $return['orders_not_paid'][] = [
                'date' => $order->datetime_finish,
                'id_order' => $order->id,
            ];
            $return['orders'][] = [
                'date' => $order->datetime_finish,
                'debit' => round($order->cost_finish_vehicle - ($order->cost_finish_vehicle * $this->procentVehicle/100)) . '*',
                'credit' => '',
                'description' => '(НЕ ОПЛАЧЕН) Сумма к выплате за заказ № ' . $order->id,
                'id_order' => $order->id,
            ];
        }

        foreach ($orders_avans as $order){
            $return['not_paid'] += round(($order->cost_finish_vehicle - $order->avans_client)
                - (($order->cost_finish_vehicle - $order->avans_client) * $this->procentVehicle/100));
            $return['balance'] += round($order->avans_client -
                ($order->avans_client * $this->procentVehicle/100));

            $return['orders_avans'][] = [
                'date' => $order->datetime_finish,
                'id_order' => $order->id,
            ];
            $return['orders'][] = [
                'date' => $order->datetime_finish,
                'debit' => round($order->avans_client -
                        ($order->avans_client * $this->procentVehicle/100))
                    . ' (' . round(($order->cost_finish_vehicle - $order->avans_client)
                        - (($order->cost_finish_vehicle - $order->avans_client) * $this->procentVehicle/100)) . ')**',
                'credit' => '',
                'description' => '(ОПЛАЧЕН КЛИЕНТОМ ЧАСТИЧНО) Сумма к выплате за заказ № ' . $order->id,
                'id_order' => $order->id,
            ];
        }

        foreach ($payments as $payment){
            if($payment->direction == Payment::DEBIT) {
                $return['balance'] += $payment->cost;
            }
            if($payment->direction == Payment::CREDIT) {
                $return['balance'] -= $payment->cost;
            }

            $return['orders'][] = [
                'date' => $payment->date,
                'credit' => $payment->cost,
                'debit' => '',
                'description' => $payment->comments,
                'id_order' => '',
                'id_paiment' => $payment->id
            ];
        }

        array_multisort($return['orders'], SORT_DESC);
        return $return;
    }

    public function getProcentVehicle(){
        //переделать в зависмимости от роли или статуса, пока у все 10
        return SettingVehicle::find()->one()->procent_vehicle;
    }

    public function hasFinishOrderOfCompany($id_company) : bool{
        return Order::find([
            'id_user' => $this->id_user,
            'id_company' => $id_company,
            ['in', 'status', Order::STATUS_CONFIRMED_VEHICLE, Order::STATUS_CONFIRMED_CLIENT]
        ])->count();
    }
}

