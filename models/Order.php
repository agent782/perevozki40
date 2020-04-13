<?php

namespace app\models;

use app\components\functions\emails;
use app\components\functions\functions;
use app\models\setting\Setting;
use app\models\setting\SettingVehicle;
use app\models\setting\SettingClient;
use function Couchbase\fastlzCompress;
use FontLib\Table\Type\post;
use Yii;
use app\components\DateBehaviors;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\components\widgets\ShowMessageWidget;


/**
 * This is the model class for table "Orders".
 *
 * @property integer $id
 * @property integer $id_vehicle_type
 * @property double $tonnage
 * @property double $length
 * @property double $width
 * @property double $height
 * @property double $volume
 * @property integer $longlength
 * @property integer $passengers
 * @property integer $ep
 * @property integer $rp
 * @property integer $lp
 * @property double $tonnage_spec
 * @property double $length_spec
 * @property double $volume_spec
 * @property string $cargo
 * @property integer $real_body_type
 * @property array $real_loading_typies
 * @property double $real_tonnage
 * @property double $real_length
 * @property double $real_width
 * @property double $real_height
 * @property double $real_volume
 * @property integer $real_longlength
 * @property integer $real_passengers
 * @property integer $real_ep
 * @property integer $real_rp
 * @property integer $real_lp
 * @property double $real_tonnage_spec
 * @property double $real_length_spec
 * @property double $real_volume_spec
 * @property string $real_cargo
 * @property float $real_h_loading
 * @property integer $real_remove_awning
 * @property integer $real_km
 * @property integer $datetime_start
 * @property integer $datetime_finish
 * @property integer $datetime_access
 * @property integer $valid_datetime
 * @property integer $create_at
 * @property integer $update_at
 * @property integer $id_route
 * @property integer $id_route_real
 * @property integer $id_pricezone_for_vehicle
 * @property integer $id_price_zone_real
 * @property integer $id_user
 * @property integer $id_company
 * @property integer $id_vehicle
 * @property integer $id_car_owner
 * @property Profile $carOwner
 * @property integer $id_driver
 * @property float $cost
 * @property float $cost_finish
 * @property float $cost_finish_vehicle
 * @property float $finishCost
 * @property float $finishCostForVehicle
 * @property integer $discount
 * @property integer $id_payment
 * @property integer $type_payment
 * @property integer $status
 * @property integer $paid_status
 * @property integer $paid_car_owner_status
 * @property string $comment
 * @property string $statusText
 * @property string $paidText
 * @property string $shortRoute
 * @property string $clientInfo
 * @property string $clientInfoWithoutPhone
 * @property integer $FLAG_SEND_EMAIL_STATUS_EXPIRED
 * @property User $user
 * @property Profile $profile
 * @property Vehicle $vehicle
 * @property Route $route
 * @property Route $realRoute
 * @property string $paymentText
 * @property string $priceZonesWithInfo
 * @property Driver $driver
 * @property string $fullNewInfo
 * @property Company $company
 * @property string $idsPriceZonesWithPriceAndShortInfo
 * @property string $idsPriceZones
 * @property string $vehicleFioAndPhone
 * @property string $real_datetime_start
 * @property float $real_h
 * @property integer $additional_cost
 * @property Invoice $invoice
 * @property Invoice $certificate
* @property integer $id_review_vehicle
* @property integer $id_review_client
 * @property float $avans_client
 * @property bool $re
 * @property OrdersFinishContacts $finishContacts
 * @property string $comment_vehicle
 * @property bool $auto_find
 * @property integer date_paid


 */
class Order extends \yii\db\ActiveRecord
{
    const STATUS_NEW = 1;
    const STATUS_EXPIRED = 2;
    const STATUS_IN_PROCCESSING = 3;
    const STATUS_VEHICLE_ASSIGNED = 4;
    const STATUS_CANCELED = 5;
    const STATUS_CONFIRMED_VEHICLE = 6;
    const STATUS_CONFIRMED_CLIENT = 7;
    const STATUS_DISPUTE = 8;
//    const STATUS_PAID = 9;
    const STATUS_NOT_ACCEPTED = 9;
    const STATUS_WAIT = 10;
    const STATUS_CONFIRMED_SET_SUM = 11;

    const PAID_NO = 0;
    const PAID_YES = 1;
    const PAID_YES_AVANS = 2;
    const PAID_PROCCESSING = 3;

    const SCENARIO_UPDATE_TRUCK = 'update_truck';
    const SCENARIO_UPDATE_PASS = 'update_pass';
    const SCENARIO_UPDATE_MANIPULATOR = 'update_manipulator';
    const SCENARIO_UPDATE_DUMP = 'update_dump';
    const SCENARIO_UPDATE_CRANE = 'update_crane';
    const SCENARIO_UPDATE_EXCAVATOR = 'update_excavator';
    const SCENARIO_UPDATE_PAID_STATUS = 'update_paid_status';
    const SCENARIO_UPDATE_STATUS = 'update_status';

    const SCENARIO_FINISH_TRUCK = 'finish_truck';
    const SCENARIO_FINISH_PASS = 'finish_pass';
    const SCENARIO_FINISH_MANIPULATOR = 'finish_manipulator';
    const SCENARIO_FINISH_DUMP = 'finish_dump';
    const SCENARIO_FINISH_CRANE = 'finish_crane';
    const SCENARIO_FINISH_EXCAVATOR = 'finish_excavator';

    const SCENARIO_ACCESSING = 'accessing';
    const SCENARIO_NEW_ORDER = 'new_order';
    const SCENARIO_LOGIST_NEW_ORDER = 'logist_new_order';
    const SCENARIO_ADD_ID_COMPANY = 'add_id_company';

    const SCENARIO_RE_FINISH = 're_finish';

    const SCENARIO_RE_CREATE = 're_create';

    const SCENARIO_CHANGE_PAID_STATUS = 'change_paid_status';
    const SCENARIO_CHANGE_AVANS_CLIENT = 'change_avans_client';
    const SCENARIO_CHANGE_TYPE_PAYMENT = 'change_type_payment';
    const SCENARIO_CHANGE_PRICEZONE_FOR_VEHICLE = 'change_price_zone_for_vehicle';
    const SCENARIO_CHANGE_DATETIME = 'change_datetime';



    public $body_typies;
    public $loading_typies;
    public $suitable_rates;
    public $selected_rates;
    public $ClientPhone;
    public $ClientPaidCash;
    public $hand_vehicle_cost;

    /**
     * @inheritdoc
     */

    public static function tableName()
    {
        return 'Orders';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_vehicle_type','body_typies'], 'required', 'message' => 'Выберите хотя бы один из вариантов.'],
            [['loading_typies'], 'validateLoadingTypies', 'skipOnEmpty' => false],
            ['tonnage', 'validateTonnage','skipOnEmpty' => false],
            [['selected_rates', 'type_payment'], 'required', 'message' => 'Выберите хотя бы один из вариантов',
                'skipOnEmpty' => false, 'skipOnError' => false],
            [['datetime_start', 'valid_datetime', 'type_payment','datetime_finish', 'real_datetime_start', 'real_km'],
                'required'],
            ['passengers', 'validatePassengers', 'skipOnEmpty' => false],

            [['tonnage', 'tonnage_spec'], 'number', 'min' => 0.01, 'max' => 50,
                'message' => 'Ввведите число. Для ввода дробного числа используйте символ "."(точка), например : 1.5 , 3.75 и т.п.'],
            [['length'], 'number', 'min' => 0.5,'max' => 20,
                'message' => 'Ввведите число. Для ввода дробного числа используйте символ "."(точка), например : 1.5 , 3.75 и т.п.'],
            [['width', 'height'], 'number', 'min' => 0.5, 'max' => 4,
                'message' => 'Ввведите число. Для ввода дробного числа используйте символ "."(точка), например : 1.5 , 3.75 и т.п.'],
            [['volume', 'volume_spec'], 'number', 'min' => 0.01, 'max' => 200,
                'message' => 'Ввведите число. Для ввода дробного числа используйте символ "."(точка), например : 1.5 , 3.75 и т.п.'],
            [['volume_spec'], 'number', 'min' => 0.1, 'max' => 5,
                'message' => 'Ввведите число. Для ввода дробного числа используйте символ "."(точка), например : 1.5 , 3.75 и т.п.'],
            [['real_h_loading'], 'number', 'min' => 0, 'max' => 24,
                'message' => 'Ввведите число. Для ввода дробного числа используйте символ "."(точка), например : 1.5 , 3.75 и т.п.'],
            ['real_remove_awning', 'integer', 'min' => 0, 'max' => 10,
                'message' => 'Ввведите число. Для ввода дробного числа используйте символ "."(точка), например : 1.5 , 3.75 и т.п.'],

            [['id_company'],
//                'validateConfirmCompany', 'skipOnEmpty' => false
                    'safe'
            ],
            [['datetime_access', 'FLAG_SEND_EMAIL_STATUS_EXPIRED',
                'id_price_zone_for_vehicle', 'discount', 'cost_finish', 'cost_finish_vehicle',
                'ClientPhone', 'id_user', 'date_paid'],
                'safe'
            ],
            [['additional_cost','ClientPaidCash'], 'default', 'value' => '0'],
            [['real_tonnage', 'real_length', 'real_volume', 'real_passengers', 'real_tonnage_spec',
                'real_length_spec', 'real_volume_spec', 'cost', 'id_review_vehicle', 'id_review_client'], 'number' ],
            [['suitable_rates', 'datetime_access', 'id_route', 'id_route_real', 'id_price_zone_real', 'cost', 'comment'], 'safe'],
            [['id','longlength', 'ep', 'rp', 'lp', 'id_route', 'id_route_real', 'additional_cost',
                'id_payment', 'status', 'type_payment', 'passengers', 'real_km', 'id_pricezone_for_vehicle',
                'id_car_owner'], 'integer'],
            [['hand_vehicle_cost'], 'number', 'max' => '200000'],
            [['cargo', 'comment_vehicle'], 'string'],
            [['create_at', 'update_at'], 'default', 'value' => date('d.m.Y H:i')],
            ['status', 'default', 'value' => self::STATUS_NEW],
            ['paid_status', 'default', 'value' => self::PAID_NO],
            [['id_vehicle', 'id_driver'], 'required', 'message' => 'Выберите один из вариантов'],
            [['paid_status', 'paid_car_owner_status'], 'default', 'value' => self::PAID_NO],
            ['real_h_loading', 'default', 'value' => 0],
            ['real_remove_awning', 'default' , 'value' => 0],
            ['type_payment', 'validateForUser'],
            [['avans_client'], 'number'],
            [['re'], 'default', 'value' => false],
            [['auto_find'], 'default', 'value' => false],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios(); // TODO: Change the autogenerated stub
        $scenarios[self::SCENARIO_UPDATE_STATUS] = ['status', 'auto-find'];
        $scenarios[self::SCENARIO_ACCESSING] = ['id', 'id_vehicle', 'id_driver', 'id_car_owner'];
        $scenarios[self::SCENARIO_NEW_ORDER] = ['id_vehicle_type','body_typies', 'loading_typies',
            'tonnage', 'selected_rates', 'type_payment', 'datetime_start', 'valid_datetime',
            'passengers','id_company', 'status', 'create_at', 'update_at', 'auto-find'];
        $scenarios[self::SCENARIO_LOGIST_NEW_ORDER] = ['id_vehicle_type','body_typies', 'loading_typies',
            'tonnage', 'selected_rates', 'type_payment', 'datetime_start', 'valid_datetime',
            'passengers', 'status', 'create_at', 'update_at', 're'];
        $scenarios[self::SCENARIO_UPDATE_TRUCK] = [
            'body_typies', 'loading_typies', 'tonnage', 'length', 'width', 'height', 'volume', 'selected_rates', 'type_payment',
            'datetime_start', 'valid_datetime', 'id_company', 'status', 'update_at', 'longlength',
            'cargo', 'ep', 'rp', 'lp'
        ];
        $scenarios[self::SCENARIO_UPDATE_PASS] = [
            'body_typies', 'passengers', 'cargo', 'selected_rates', 'type_payment',
            'datetime_start', 'valid_datetime', 'id_company', 'status', 'update_at',
            'cargo'
        ];
        $scenarios[self::SCENARIO_UPDATE_MANIPULATOR] = [
            'tonnage', 'tonnage_spec', 'selected_rates', 'type_payment',
            'datetime_start', 'valid_datetime', 'id_company', 'status', 'update_at',
            'cargo'
        ];
        $scenarios[self::SCENARIO_UPDATE_DUMP] = [
            'tonnage', 'volume', 'selected_rates', 'type_payment',
            'datetime_start', 'valid_datetime', 'id_company', 'status', 'update_at',
            'cargo'
        ];
        $scenarios[self::SCENARIO_UPDATE_CRANE] = [
            'tonnage_spec', 'selected_rates', 'type_payment',
            'datetime_start', 'valid_datetime', 'id_company', 'status', 'update_at',
            'cargo'
        ];
        $scenarios[self::SCENARIO_UPDATE_EXCAVATOR] = [
            'volume_spec', 'selected_rates', 'type_payment',
            'datetime_start', 'valid_datetime', 'id_company', 'status', 'update_at',
            'cargo'
        ];
        $scenarios[self::SCENARIO_FINISH_TRUCK] = [
            'real_datetime_start', 'datetime_finish', 'real_km', 'additional_cost', 'comment_vehicle', 'real_h_loading',
            'real_tonnage', 'real_length', 'real_volume', 'real_remove_awning', 'ClientPaidCash', 'real_longlength'
            , 'hand_vehicle_cost', 'cost_finish', 'cost_finish_vehicle'
        ];
        $scenarios[self::SCENARIO_FINISH_PASS] = [
            'real_datetime_start', 'datetime_finish', 'real_km', 'additional_cost', 'comment_vehicle','real_h_loading',
            'real_passengers', 'ClientPaidCash', 'hand_vehicle_cost', 'cost_finish', 'cost_finish_vehicle'
        ];
        $scenarios[self::SCENARIO_FINISH_MANIPULATOR] = [
            'real_datetime_start', 'datetime_finish', 'real_km', 'additional_cost', 'comment_vehicle','real_h_loading',
            'real_tonnage', 'real_length', 'real_tonnage_spec', 'real_length_spec', 'ClientPaidCash', 'hand_vehicle_cost'
            , 'cost_finish', 'cost_finish_vehicle'
        ];
        $scenarios[self::SCENARIO_FINISH_DUMP] = [
            'real_datetime_start', 'datetime_finish', 'real_km', 'additional_cost', 'comment_vehicle','real_h_loading',
            'real_tonnage', 'real_volume', 'ClientPaidCash', 'hand_vehicle_cost', 'cost_finish', 'cost_finish_vehicle'
        ];
        $scenarios[self::SCENARIO_FINISH_CRANE] = [
            'real_datetime_start', 'datetime_finish', 'real_km', 'additional_cost', 'comment_vehicle','real_h_loading',
            'real_tonnage_spec', 'real_length_spec', 'ClientPaidCash', 'hand_vehicle_cost', 'cost_finish', 'cost_finish_vehicle'
        ];
        $scenarios[self::SCENARIO_FINISH_EXCAVATOR] = [
            'real_datetime_start', 'datetime_finish', 'real_km', 'additional_cost', 'comment_vehicle','real_h_loading',
            'real_volume_spec', 'ClientPaidCash', 'hand_vehicle_cost', 'cost_finish', 'cost_finish_vehicle'
        ];
        $scenarios[self::SCENARIO_ADD_ID_COMPANY] = ['id_company'];
        $scenarios[self::SCENARIO_UPDATE_PAID_STATUS] = ['paid_status'];
        $scenarios[self::SCENARIO_RE_FINISH] = [
            'id_user', 'id_vehicle', 'id_driver', 'id_car_owner', 'id_vehicle_type', 'body_typies',
            'id_pricezone_for_vehicle', 'real_longlength',
            'real_tonnage', 'real_length', 'real_volume',
            'real_passengers',
            'real_tonnage','real_length', 'real_tonnage_spec', 'real_length_spec', 'real_volume', 'real_volume_spec',
            'real_datetime_start', 'datetime_finish', 'type_payment', 'real_km', 'real_h_loading', 'additional_cost',
            'comment', 'cost', 'cost_finish', 'cost_finish_vehicle', 'real_remove_awning',
            'create_at', 'update_at',
            'id_route_real', 'id_pricezone_for_vehicle', 'id_price_zone_real',
            'cost', 'cost_finish', 'cost_finish_vehicle', 're'
        ];
        $scenarios[self::SCENARIO_RE_CREATE] = [
            'id_user','id_vehicle_type',
            'tonnage', 'length', 'volume', 'passengers',
            'tonnage','length', 'tonnage_spec', 'length_spec', 'volume', 'volume_spec',
            'type_payment', 'datetime_start',
            'passengers','id_company', 'status', 'create_at', 'update_at', 'comment'
        ];
        $scenarios[self::SCENARIO_CHANGE_PAID_STATUS] = ['paid_status', 'date_paid'];
        $scenarios[self::SCENARIO_CHANGE_AVANS_CLIENT] = ['avans_client', 'date_paid'];
        $scenarios[self::SCENARIO_CHANGE_TYPE_PAYMENT] = ['type_payment'];
        $scenarios[self::SCENARIO_CHANGE_PRICEZONE_FOR_VEHICLE] = ['id_pricezone_for_vehicle'];
        $scenarios[self::SCENARIO_CHANGE_DATETIME] = ['datetime_start'];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    
    public function attributeLabels()
    {
        return [
            'id' => '№',
            'id_vehicle_type' => 'Тип транспорта.',
            'body_typies' => 'Тип кузова.',
            'loading_typies' => 'Тип погрузки/выгрузки.',
            'tonnage' => 'Необходимая грузоподъемность ТС в тоннах.',
            'length' => 'Необходимая длина кузова в метрах.',
            'width' => 'Необходимая ширина кузова в метрах.',
            'height' => 'Необходимая высота кузова в метрах.',
            'volume' => 'Необходимый объем кузова в м3.',
            'longlength' => 'Груз длинномер.',
            'passengers' => 'Количество пассажиров',
            'ep' => 'Количество "европоддонов" 1,2м х 0,8м.',
            'rp' => 'Количество "русских" поддонов 1м х 1,2м.',
            'lp' => 'Количество нестандартных поддонов 1,2м х 1,2м.',
            'tonnage_spec' => 'Необходимая грузоподъемность механизма(стрелы).',
            'length_spec' => 'Необходимая длина механизма(стрелы).',
            'volume_spec' => 'Необходимый объем механизма(ковша).',
            'cargo' => 'Комментарии. Описание груза.',
            'datetime_start' => 'Дата и время подачи ТС.',
            'datetime_finish' => 'Дата и время возвращения в г.Обнинск после завершения заказа.',
            'datetime_access' => 'Дата и  время подтверждения Заказчиком.',
            'valid_datetime' => 'Выполнять поиск ТС до:',
            'id_route' => 'Id маршруты',
            'id_route_real' => 'Id реального маршрута',
            'type_payment' => 'Способ оплаты:',
            'status' => 'Статус',
            'statusText' => 'Статус',
            'paidText' => 'Оплата от Клиента',
            'create_at' => 'Дата оформления заказа',
            'clientInfo' => 'Заказчик',
            'shortInfoForClient' => 'ТС',
            'paymentText' => 'Тип оплаты',
            'priceZonesWithInfo' => 'Тарифы',
            'id_vehicle' => 'ТС',
            'id_driver' => 'Водитель',
            'real_h' => 'Время работы',
            'real_km' => 'Фактический пробег (км.)',
            'comment_vehicle' => 'Комментарий водителя',
            'real_datetime_start' => 'Фактическое время выезда',
            'real_tonnage' => 'Фактический вес',
            'real_length' => 'Требуемая длина кузова',
            'real_volume' => 'Требуемый объем',
            'real_h_loading' => 'Общее время потраченное на разгрузку, погрузку, ожидание (для заказов с пробегом более 120 км), ч. ',
            'real_remove_awning' => 'Количество "растентовок" одной стороны сбоку или сверху',
            'real_passengers' => 'Количество пассажиров',
            'real_tonnage_spec' => 'Фактический вес груза (на стреле)',
            'real_length_spec' => 'Минимальная требуемая длина стрелы',
            'real_volume_spec' => 'Объем ковша',
            'additional_cost' => 'Доп. расходы (Помощь грузчика(ов), платные дороги/въезды и т.п.), р.',
            'cost' => 'Сумма',
            'ClientPhone' => 'Телефон клиента',
            'id_company' => 'Юр. лицо ',
            'avans_client' => 'Аванс',
            'cost_finish' => 'Сумма для клиента',
            'cost_finish_vehicle' => 'Сумма для водителя',
            'comment' => 'Комментарии:',
            'old_id' => 'Усл. обозначение'

        ];
    }

    public function behaviors()
    {
        return [
            'convertDateTime' => [
                'class' => 'app\components\DateBehaviors',
                'dateAttributes' => ['real_datetime_start', 'datetime_start', 'datetime_finish','datetime_access' ,
                    'valid_datetime', 'create_at', 'update_at'],
                'format' => DateBehaviors::FORMAT_DATETIME,
            ],
            'convertDate' => [
                'class' => 'app\components\DateBehaviors',
                'dateAttributes' => ['date_paid'],
                'format' => DateBehaviors::FORMAT_DATE,
            ],
        ];
    }

    public function validateConfirmCompany($attribute){
        $id_company = $this->$attribute;
        $id_user = Yii::$app->user->id;

        if($id_company) {
            $modelDocument = \app\models\Document::findOne(['id_company' => $id_company, 'type' => \app\models\Document::TYPE_CONTRACT_CLIENT]);
            $modelPOA = XprofileXcompany::find()->where(['id_profile' => $id_user, 'id_company' => $id_company])->one();
            if ($modelDocument->status !== Document::STATUS_SIGNED || $modelPOA->STATUS_POA !== XprofileXcompany::STATUS_POWER_OF_ATTORNEY_SIGNED) {
                $this->addError($attribute, 'Не оформлен Договор с этим юр. лицом или отсутствует доверенность. 
                Перейдите в раздел Юридические лица в личном кабинете.');
            }
            return;
        } else{
            if($this->type_payment == Payment::TYPE_BANK_TRANSFER){
                $this->addError($attribute, 'Выберите плательщика.');
                return;
            }
        }
    }

    public  function validatePassengers($attribute){
        if($this->id_vehicle_type == Vehicle::TYPE_PASSENGER && !$this->$attribute){
            $this->addError($attribute, 'Укажите количество пассажиров');
        }
        return;
    }

    public function validateLoadingTypies($attribute){
        if($this->id_vehicle_type == Vehicle::TYPE_TRUCK && !$this->$attribute){
            $this->addError($attribute, 'Выберите хотя бы один из вариантов.');
        }
        return;
    }

    public function validateTonnage($attribute){
        if(is_array($this->body_typies)) {
            if (($this->id_vehicle_type == Vehicle::TYPE_TRUCK
                    || in_array(Vehicle::BODY_dump, $this->body_typies)
                    || in_array(Vehicle::BODY_manipulator, $this->body_typies)
//                || $this->body_typies[1] == Vehicle::BODY_manipulator
                )
                && !$this->$attribute
            ) {
                $this->addError($attribute, 'Необходимо заполнить "Общий вес груза".');
            }
            return;
        }
        if (($this->id_vehicle_type == Vehicle::TYPE_TRUCK)
            && !$this->$attribute
        ) {
            $this->addError($attribute, 'Необходимо заполнить "Общий вес груза".');
        }
        return;
    }

    public function validateForUser($attribute){
        if(Yii::$app->user->can('user')
            && $this->type_payment != Payment::TYPE_CASH
        ){
            $this->addError($attribute, 'Вам необходимо '
                . Html::a('завершить регистрацию Клиента.', '/user/signup-client')
                . 'Это займет у Вас 1 минуту. До этого Вы можете выбрать только наличную форму оплаты. '
            );
        }
    }

    public function getSuitableRates($distance, int $limit = 10){
        $result = [];
        $priceZones = PriceZone::find()->where(['veh_type' => $this->id_vehicle_type])
            ->andWhere(['status' => PriceZone::STATUS_ACTIVE]);

        switch ($this->id_vehicle_type) {
            case Vehicle::TYPE_TRUCK:
                $priceZones = $priceZones
                    ->andFilterWhere(['>=', 'tonnage_max', $this->tonnage])
                    ->andFilterWhere(['>=', 'volume_max', $this->volume])
                    ->andFilterWhere(['>=', 'length_max', $this->length]);
                if(!$this->longlength) {
                    $priceZones = $priceZones->andFilterWhere(['longlength' => $this->longlength]);
                }
//                else {
//                    $priceZones = $priceZones->andFilterWhere(['>', 'length_max', $this->length - 2]);
//                }
//                    ->andFilterWhere(['longlength' => $this->longlength])
                $priceZones = $priceZones->orderBy(['r_km'=>SORT_ASC, 'r_h'=>SORT_ASC])
                    ->all()
                ;
                break;
            case Vehicle::TYPE_PASSENGER:
                $priceZones = $priceZones
                    ->andFilterWhere(['>=', 'passengers', $this->passengers])
                    ->orderBy(['r_km'=>SORT_ASC, 'r_h'=>SORT_ASC])
                    ->all()
                ;
                break;
            case Vehicle::TYPE_SPEC:
                switch ($this->body_typies[1]){
                    case Vehicle::BODY_manipulator:
                        $priceZones = $priceZones
                            ->andFilterWhere(['>', 'tonnage_spec_max', $this->tonnage_spec])
                            ->andFilterWhere(['>', 'length_spec_max', $this->length_spec])
                            ->andFilterWhere(['>', 'tonnage_max', $this->tonnage])
                            ->andFilterWhere(['>', 'length_max', $this->length])
                        ;
                        break;
                    case Vehicle::BODY_dump:
                        $priceZones = $priceZones
                            ->andFilterWhere(['>', 'tonnage_max', $this->tonnage])
                            ->andFilterWhere(['>', 'volume_max', $this->volume])
                        ;
                        break;
                    case Vehicle::BODY_crane:
                        $priceZones = $priceZones
                            ->andFilterWhere(['>', 'tonnage_spec_max', $this->tonnage_spec])
                            ->andFilterWhere(['>', 'length_spec_max', $this->length_spec])
                        ;
                        break;
                    case Vehicle::BODY_excavator:
                        $priceZones = $priceZones
                            ->andFilterWhere(['>=', 'volume_spec', $this->volume_spec])
                        ;
                        break;
                    case Vehicle::BODY_excavator_loader:
                        $priceZones = $priceZones
                            ->andFilterWhere(['>=', 'volume_spec', $this->volume_spec])
                        ;
                        break;
                }
                $priceZones = $priceZones
                    ->orderBy(['r_km'=>SORT_ASC, 'r_h'=>SORT_ASC])
                    ->all()
                ;
                break;
        }

//        $priceZones = $priceZones->all();
        $count = 0;
        foreach ($priceZones as $priceZone) {
            if($count >= $limit) return $result;

            foreach ($this->body_typies as $body_type) {
                if ($priceZone->hasBodyType($body_type)
                    && !array_key_exists($priceZone->unique_index, $result)) {
                    $result[$priceZone->unique_index] = 'Тарифная зона ' . $priceZone->id;
                    $count++;
                }
            }
        }
        return $result;
    }

    public function getSuitableRatesCheckboxList($distance = null, $discount = null, $limit= 10){
        $suitable_rates = $this->getSuitableRates($distance, $limit);
        $return = [];
        foreach ($suitable_rates as $id => $suitable_rate){
            $PriceZone = PriceZone::findOne(['unique_index' => $id]);
            $return[$PriceZone->unique_index] = ' &asymp; '
                . $PriceZone->CostCalculationWithDiscountHtml($distance,$discount)
                . ' руб.* '
                . ShowMessageWidget::widget([
                    'ToggleButton' => ['label' => 'Тариф №' . $PriceZone->id],
                    'helpMessage' => $PriceZone->getWithDiscount($discount)->printHtml(),
                    'header' => 'Тарифная зона №' . $PriceZone->id,
//                    'ToggleButton' => ['label' => '<img src="/img/icons/help-25.png">', 'class' => 'btn'],
                ])
            ;
        }
        return $return;
    }

    public function getListPriceZonesCostsWithDiscont($distance = null, $discount = null, $infoButton = true){
//        return $this->getPriceZones()->one()->r_km;
        $rates = $this->priceZones;
        $return = '<ul>';
        foreach ($rates as $PriceZone){
            $return .= '<li>';
            $return .= $PriceZone->getTextWithShowMessageButton($distance, $infoButton, $discount);
            $return .= '</li>';
        }
        return $return . '</ul>';
    }

    public function getListPriceZonesCostsForVehicle($id_car_owner, $distance = null, $infoButton = true){
        $rates = $this->priceZones;
        $return = '<ul>';
        foreach ($rates as $PriceZone){
            $PZ = $PriceZone->getPriceZoneForCarOwner($id_car_owner);
            $return .= '<li>';
            $return .= $PZ->getTextWithShowMessageButton($distance , $infoButton);
//
            $return .= '</li>';
        }
        return $return . '</ul>';
    }

    public function getDiscount($user_id = null){
        if(!$this->type_payment) return 0;
        $type_payment = $this->type_payment;
        $SettingClient = SettingClient::find()->limit(1)->one();
        $discount_cash = 0;
        $discount_card = 0;
        if(!$user_id ) {
            $discount_cash = $SettingClient->not_registered_discount_cash;
            $discount_card = $SettingClient->not_registered_discount_card;
        } else {
            $user = User::findOne(['id' => $user_id]);
            if ($user->canRole('user')) {
                $discount_cash = $SettingClient->user_discount_cash;
                $discount_card = $SettingClient->user_discount_card;
            }
            else if ($user->canRole('client') || $user->canRole('car_owner')) {
                $discount_cash = $SettingClient->client_discount_cash;
                $discount_card = $SettingClient->client_discount_card;
            }
            else if ($user->canRole('vip_client') || $user->canRole('vip_car_owner')) {
                $discount_cash = $SettingClient->vip_client_discount_cash;
                $discount_card = $SettingClient->vip_client_discount_card;
            } else {
                $discount_cash = $SettingClient->not_registered_discount_cash;
                $discount_card = $SettingClient->not_registered_discount_card;
            }
        }
        if($type_payment == Payment::TYPE_CASH) return $discount_cash;
        if($type_payment == Payment::TYPE_SBERBANK_CARD) return $discount_card;
        return 0;
    }

    public function getFinishPriceZone(){
        $result = [];
        $priceZones = PriceZone::find()->where(['veh_type' => $this->id_vehicle_type])
            ->andFilterWhere(['status' => PriceZone::STATUS_ACTIVE]);
        if(!$priceZones->count()) return false;

        switch ($this->id_vehicle_type) {
            case Vehicle::TYPE_TRUCK:
                $priceZones = $priceZones
                    ->andFilterWhere(['>=', 'tonnage_max', $this->real_tonnage])
                    ->andFilterWhere(['>=', 'volume_max', $this->real_volume])
                    ->andFilterWhere(['>=', 'length_max', $this->real_length])
                    ->andFilterWhere(['longlength' => $this->real_longlength])
//                    ->orderBy(['r_km'=>SORT_ASC, 'r_h'=>SORT_ASC])
//                    ->all()
                ;
                    if(!$this->real_longlength) {
                        $priceZones = $priceZones->andFilterWhere(['longlength' => $this->real_longlength]);
                    }
                $priceZones = $priceZones
                    ->orderBy(['r_km'=>SORT_ASC, 'r_h'=>SORT_ASC])
                    ->all()
                ;

                break;
            case Vehicle::TYPE_PASSENGER:
                $priceZones = $priceZones
                    ->andFilterWhere(['>=', 'passengers', $this->real_passengers])
                    ->orderBy(['r_km'=>SORT_ASC, 'r_h'=>SORT_ASC])
                    ->all()
                ;
                break;
            case Vehicle::TYPE_SPEC:
                switch ($this->body_typies[0]){
                    case Vehicle::BODY_manipulator:
                        $priceZones = $priceZones
                            ->andFilterWhere(['>=', 'tonnage_spec_max', $this->real_tonnage_spec])
                            ->andFilterWhere(['>=', 'length_spec_max', $this->real_length_spec])
                            ->andFilterWhere(['>=', 'tonnage_max', $this->real_tonnage])
                            ->andFilterWhere(['>=', 'length_max', $this->real_length])
                        ;
                        break;
                    case Vehicle::BODY_dump:
                        $priceZones = $priceZones
                            ->andFilterWhere(['>=', 'tonnage_max', $this->real_tonnage])
                            ->andFilterWhere(['>=', 'volume_max', $this->real_volume])
                        ;
                        break;
                    case Vehicle::BODY_crane:
                        $priceZones = $priceZones
                            ->andFilterWhere(['>=', 'tonnage_spec_max', $this->real_tonnage_spec])
                            ->andFilterWhere(['>=', 'length_spec_max', $this->real_length_spec])
                        ;
                        break;
                    case Vehicle::BODY_excavator:
                        $priceZones = $priceZones
                            ->andFilterWhere(['>=', 'volume_spec', $this->real_volume_spec])
                        ;
                        break;
                    case Vehicle::BODY_excavator_loader:
                        $priceZones = $priceZones
                            ->andFilterWhere(['>=', 'volume_spec', $this->real_volume_spec])
                        ;
                        break;
                }
                $priceZones = $priceZones
                    ->orderBy(['r_km'=>SORT_ASC, 'r_h'=>SORT_ASC])
                    ->all()
                ;
                break;
        }

        foreach ($priceZones as $priceZone) {
            if ($priceZone->hasBodyType($this->vehicle->bodyType->id)){
                $result[] = $priceZone;
            }
        }
        $pricezone_for_vehicle = PriceZone::findOne(['unique_index' => $this->id_pricezone_for_vehicle]);
        if(!$result) return $pricezone_for_vehicle->id;
        $real_pricezone = $result[0];
        $cost_km = $real_pricezone->r_km;
        $cost_h = $real_pricezone->r_h;
        foreach ($result as $res){
            if(($cost_km > $res->r_km || $cost_h > $res->r_h)){
                $cost_km = $res->r_km;
                $cost_h = $res->r_h;
                $real_pricezone = $res;
            }
        }
//        return $real_pricezone->id;
        if($real_pricezone && $pricezone_for_vehicle) {
            if ($real_pricezone->r_km < $pricezone_for_vehicle->r_km
                || $real_pricezone->r_h < $pricezone_for_vehicle->r_h) {
                $real_pricezone = $pricezone_for_vehicle;
            }
        }
        return $real_pricezone->unique_index;
    }

    public function afterSave($insert, $changedAttributes)
    {
        if($this->type_payment != Payment::TYPE_BANK_TRANSFER){
            $this->id_company = null;
        }

        if ($insert) {
            if(count($this->body_typies)) {
                foreach ($this->body_typies as $body_type_ld) {
                    if($body_type_ld) {
                        $BodyType = BodyType::findOne(['id' => $body_type_ld]);
                        if(!$this->hasBodyType($BodyType))
                            $this->link('bodyTypies', $BodyType);
                    }
                }
            }
            if(count($this->loading_typies)) {
                foreach ($this->loading_typies as $loading_type_id) {
                    if ($loading_type_id) { //что бы не сохранялся Любой
                        $LoadingType = LoadingType::findOne(['id' => $loading_type_id]);
                        $this->link('loadingTypies', $LoadingType);
                    }
                }
            }
            if($this->selected_rates) {
                foreach ($this->selected_rates as $selected_rate_unique_index) {
                    $PriceZone = PriceZone::findOne(['unique_index' => $selected_rate_unique_index]);
                    $this->link('priceZones', $PriceZone);
                }
            }
            parent::afterSave($insert, $changedAttributes);
            if(!$this->re) {
                $this->changeStatus(self::STATUS_NEW, $this->id_user);
            }
//
        }else {
            if(count($this->body_typies)) {
                $this->unlinkAll('bodyTypies', true);
                foreach ($this->body_typies as $body_type_ld) {
                    if($body_type_ld) {
                        $BodyType = BodyType::findOne(['id' => $body_type_ld]);
                        $this->link('bodyTypies', $BodyType);
                    }
                }
            }
            if(count($this->loading_typies)) {
                $this->unlinkAll('loadingTypies', true);
                foreach ($this->loading_typies as $loading_type_id) {
                    if ($loading_type_id) { //что бы не сохранялся Любой
                        $LoadingType = LoadingType::findOne(['id' => $loading_type_id]);
                        $this->link('loadingTypies', $LoadingType);
                    }
                }
            }
            if($this->selected_rates) {
                $this->unlinkAll('priceZones', true);
                foreach ($this->selected_rates as $selected_rate_id) {
                    $PriceZone = PriceZone::findOne(['unique_index' => $selected_rate_id]);
                    $this->link('priceZones', $PriceZone);
                }
            }
//            emails::sendToAdminChangeOrder($this->id);
            parent::afterSave($insert, $changedAttributes);
        }
    }

    public function afterFind()
    {
        $this->body_typies = ArrayHelper::getColumn($this->bodyTypies, 'id');
        $this->loading_typies = ArrayHelper::getColumn($this->loadingTypies, 'id');
        if($this->suitable_rates) {
            $this->suitable_rates = self::getSuitableRates($this->route->distance);
        }
        $this->selected_rates = ArrayHelper::getColumn($this->priceZones, 'unique_index');
        parent::afterFind(); // TODO: Change the autogenerated stub
    }

    public function beforeDelete()
    {
        $this->unlinkAll('bodyTypies', true);
        $this->unlinkAll('loadingTypies', true);
        $this->unlinkAll('priceZones', true);
        $this->unlinkAll('route', true);
        $this->unlinkAll('realRoute', true);
        $this->deleteEventChangeStatusToExpired();
        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }

    public function setStatus($status){
        $this->status = $status;
        return $this->update();
    }

    public function getVehicleType(){
        return $this->hasOne(VehicleType::className(), ['id' => 'id_vehicle_type']);
    }

    public function getInvoice(){
        return $this->hasOne(Invoice::class, ['id_order' => 'id'])
            ->andWhere([invoice::tableName().'.type' => Invoice::TYPE_INVOICE])
            ;
    }

    public function getCertificate(){
        return $this->hasOne(Invoice::class, ['id_order' => 'id'])
            ->andWhere([invoice::tableName().'.type' => Invoice::TYPE_CERTIFICATE])
            ;
    }

    public function getBodyTypies(){
        return $this->hasMany(BodyType::className(), ['id' => 'id_bodytype'])
            ->viaTable('XorderXtypebody', ['id_order' => 'id']);
    }

    public function getBodyTypiesText(){
        if($this->getBodyTypies()->count()){
            $return = '';
            foreach ($this->getBodyTypies()->all() as $bodyType){
                $return .= $bodyType->body . ', ';
            }
            return $return;
        }
        return false;
    }

    public function getLoadingTypies(){
        return $this->hasMany(LoadingType::className(), ['id' => 'id_loading_type'])
            ->viaTable('XorderXloadingtype', ['id_order' => 'id']);
    }

    public function getFinishContacts(){
        return $this->hasOne(OrdersFinishContacts::class, ['id_order' => 'id']);
    }

    public function getPriceZones(){
        return $this->hasMany(PriceZone::className(), ['unique_index' => 'id_rate'])
            ->viaTable('XorderXrate', ['id_order' => 'id'])
//            ->andWhere([PriceZone::tableName() . '.status' => PriceZone::STATUS_ACTIVE])
            ->orderBy('r_km', 'r_h')
            ;
    }

    public function getIdsPriceZones(){
        $return = '';
        foreach ($this->priceZones as $priceZone){
            $return .= $priceZone->id . ', ';
        }
        $return = substr($return, 0, -2);
        return $return;
    }

    public function getIdsPriceZonesWithPriceAndShortInfo(){
        $return = '<ul>';
        $distance = $this->route->distance;
        foreach ($this->priceZones as $priceZone){
            $return .= '<li>' . $priceZone-> getPriceAndShortInfo($distance) . '</li>';
        }
        $return .= '</ul>';

        return $return;
    }

    public function getRoute(){
        return $this->hasOne(Route::className(), ['id' => 'id_route']);
    }

    public function getRealRoute(){
        return $this->hasOne(Route::className(), ['id' => 'id_route_real']);
    }

    public function getUser(){
        return $this->hasOne(User::className(), ['id' => 'id_user']);
    }

    public function getProfile(){
        return $this->hasOne(Profile::className(), ['id_user' => 'id_user']);
    }

    public function getCompany(){
        return $this->hasOne(Company::class, ['id' => 'id_company']);
    }

    public function getCarOwner(){
        return $this->hasOne(Profile::class, ['id_user' => 'id_car_owner']);
    }

    public function getVehicle(){
        if($this->id_vehicle){
            return $this->hasOne(Vehicle::className(), ['id'=> 'id_vehicle']);
        }
        return null;
    }

    public function getDriver(){
        if($this->id_driver){
            return $this->hasOne(Driver::class, ['id' => 'id_driver']);
        }
    }

    public function getReviewVehicle(){
        return $this->hasOne(Review::class, ['id' => 'id_review_vehicle']);
    }

    public function getReviewClient(){
        return $this->hasOne(Review::class, ['id' => 'id_review_client']);
    }

    public function getStatusText(){
        $res = 'Новый';
        switch ($this->status){
            case self::STATUS_NOT_ACCEPTED:
                $res = 'Не принят';
                break;
            case self::STATUS_EXPIRED:
                $res = 'ТС не найдено';
                break;
            case self::STATUS_IN_PROCCESSING:
                $res = 'В обработке';
                break;
            case self::STATUS_VEHICLE_ASSIGNED:
                $res = 'Водитель принял заказ';
                break;
            case self::STATUS_CANCELED:
                $res = 'Отменен';
                break;
            case self::STATUS_CONFIRMED_VEHICLE:
                $res = 'Выполнен. Подтвержден водителем.';
                break;
            case self::STATUS_CONFIRMED_CLIENT:
                $res = 'Выполнен. Подтвержден клиентом.';
                break;
            case self::STATUS_DISPUTE:
                $res = 'Открыт спор';
                break;
        }
        return $res;
    }

    static public function getStatusesArray(){
       $res[self::STATUS_NEW] = 'Новый';
       $res[self::STATUS_IN_PROCCESSING] = 'В обработке';
       $res[self::STATUS_VEHICLE_ASSIGNED] = 'Принят водителем';
       $res[self::STATUS_EXPIRED] = 'ТС не найдено';
       $res[self::STATUS_CANCELED] = 'Отменен';
       $res[self::STATUS_CONFIRMED_VEHICLE] = 'Выполнен';
       $res[self::STATUS_CONFIRMED_CLIENT] = 'Подтвержден клиентом';
       $res[self::STATUS_NOT_ACCEPTED] = 'Не принят';
//       $res[self::STATUS_DISPUTE] = 'Открыт спор';

        return $res;
    }

    public function getPaidText(){
       switch ($this->paid_status){
           case self::PAID_NO:
               return 'Не оплачен';
               break;
           case self::PAID_YES:
               return 'Оплачен';
               break;
           case self::PAID_YES_AVANS:
               return 'Частично оплачен';
               break;
       }
       return null;
    }

    public function getPaidCarOwnerText(){
        switch ($this->paid_car_owner_status){
            case self::PAID_NO:
                return 'Не оплачен';
                break;
            case self::PAID_YES:
                return 'Оплачен';
                break;
            case self::PAID_YES_AVANS:
                return 'Частично оплачен';
                break;
        }
        return null;
    }

    public function hasBodyType($bodyType){
        foreach ($this->bodyTypies as $bType) {
            if($bodyType->id == $bType->id) return true;
        }
        return false;
    }

    public function getShortInfoForClient($Html = false, $finish = false){
        $return = 'Тип ТС: ' . $this->vehicleType->type .'.<br> Тип(ы) кузова: ';
        $bTypies =  '';
        if($finish && $this->vehicle){
            $bTypies .= $this->vehicle->bodyTypeText . ', ';
        } else {
            if(!$Html) {
                foreach ($this->body_typies as $bodyType) {
                    $bTypies .= BodyType::findOne($bodyType)->body . ', ';
                }
                $bTypies = substr($bTypies, 0, -2);
            } else {
                foreach ($this->body_typies as $bodyType) {
                    $bTypies .= BodyType::findOne($bodyType)->getBodyShortWithTip() . ', ';
                }
                $bTypies .=' ' . ShowMessageWidget::widget([
                    'helpMessage' => $this->bodyTypiesText
                ]);
            }

        }
        $tonnage = ($finish)? $this->real_tonnage: $this->tonnage;
        $length = ($finish)? $this->real_length: $this->length;
        $height= ($finish)? null: $this->height;
        $width= ($finish)? null: $this->width;
        $volume= ($finish)? $this->real_volume: $this->volume;
        $passengers= ($finish)? $this->real_passengers: $this->passengers;
        $tonnage_spec = ($finish)? $this->real_tonnage_spec: $this->tonnage_spec;
        $length_spec = ($finish)? $this->real_length_spec: $this->length_spec;
        $volume_spec = ($finish)? $this->real_volume_spec: $this->volume_spec;
        $longlength = ($finish)? $this->real_longlength : $this->longlength;
        $return .= $bTypies . '<br>';
        switch ($this->id_vehicle_type) {
            case Vehicle::TYPE_TRUCK:
                $return .= 'Вес: ' . $tonnage . ' т. ';
//                $return .= ' Длина: ';
                $return .= ($length) ? $length . 'м *' : '--.  * ';
                $return .= ($height) ? $height . 'м * ' : '-- * ';
                $return .= ($width) ? $width . 'м ' : '-- ';
                $return .= ' (Д*В*Ш)';
                $return .= 'Объем: ';
                $return .= ($volume) ? $volume . ' м3 ' : '-- ';
                $return .= ($longlength) ? ' Груз-длинномер.<br>' : '<br>';
                $lTypies = 'Погрузка/разгрузка: ';
                if ($this->loading_typies) {

                    foreach ($this->loading_typies as $loadingType) {
                        $lTypies .= LoadingType::findOne($loadingType)->type . ', ';
                    }
                    $lTypies = substr($lTypies, 0, -2) . '.';
                    $return .= ($finish) ? '' : $lTypies . '. <br>';
                }
                break;
            case Vehicle::TYPE_PASSENGER:
                $return .= $passengers . ' пассажира(ов)';
                break;

            case Vehicle::TYPE_SPEC:
                switch ($this->bodyTypies[0]->id) {
                    case Vehicle::BODY_manipulator:
                        $return .= 'Вес: ' . $tonnage . ' т. ';
                        $return .= ($length)?$length. 'м * ':'--.  * ';
                        $return .= ($width)?$width . 'м ':'-- ';
                        $return .= '(Д*Ш). ';
                        $return .= 'Стрела: ';
                        $return .= ($tonnage_spec)?$tonnage_spec . ' т., ': '-- т., ';
                        $return .= ($length_spec)?$length_spec . ' м.': '-- м.';
                        break;
                    case Vehicle::BODY_dump:
                        $return .= 'Вес: ' . $tonnage . ' т. ';
                        $return .= 'Объем: ' . $volume . ' м3. ';
                        break;
                    case Vehicle::BODY_crane:
                        $return .= 'Стрела: ';
                        $return .= ($tonnage_spec)?$tonnage_spec . ' т., ': '-- т., ';
                        $return .= ($length_spec)?$length_spec . ' м.': '-- м.';
                        break;
                    case Vehicle::BODY_excavator:
                        $return .= 'Ковш: ';
                        $return .= ($volume_spec)?$volume_spec . ' м3. ': '-- м3.';
                        break;
                    case Vehicle::BODY_excavator_loader:
                        $return .= 'Ковш: ';
                        $return .= ($volume_spec)?$volume_spec . ' м3. ': '-- м3.';
                        break;
                }
                break;
        }
        $return .= ($this->cargo && !$finish)?'<br>Комментарии: ' . $this->cargo : '';
        return $return;
    }

    static public function getCountNewOrders(){
        return Order::find()
            ->where(['status' => self::STATUS_NEW])
            ->orWhere(['status' => self::STATUS_IN_PROCCESSING])
            ->count();
    }

    public function getShortRoute(bool $real = false){
        $route = ($real) ? $this->realRoute : $this->route;
        $return = '';
        if(!$real) $return .= '(&asymp;' . $route->distance . 'km)* <br>';
        $return .= $route->startCity . ' <br>';
        for($i = 1; $i<9; $i++){
            $attribute = 'route' . $i;
            if($route->$attribute) {
                $return .=  '- ... <br>';
                break;
            }
        }
        $return .=  '- '.$route->finishCity ;
        return $return;
    }

    public function getClientInfo($html = true, $phone = true){
        $return = '';
        if($FC = $this->finishContacts){
            if($company = $this->company) {
                $return .= $this->company->name . '<br>';
            }
            $return .= $FC->getClientInfo($phone);
            return $return;
        }
        if($this->profile && $this->user) {
            $return .= $this->profile->fioFull;
            if($phone) {
                $return .= '<br>'
                    . 'Телефон: ' . functions::getHtmlLinkToPhone($this->user->username, $html);
                if ($this->profile->phone2) $return .= ' (доп. тел.: ' . functions::getHtmlLinkToPhone($this->profile->phone2, $html);
            }
        }
        if($this->id_company) $return .= '<br>' .$this->company->name . '<br>';

        return $return;
    }

    public function getVehicleFioAndPhone(){
        $return = $this->vehicle->profile->fioFull . '<br>'
            . 'Телефон: <a href="tel:+7'. $this->vehicle->profile->phone .'">'. $this->vehicle->profile->phone . ' </a>';
            $return .= ($this->vehicle->profile->phone2)
                ? '(доп. тел.: <a href="tel:+7'. $this->vehicle->profile->phone2 .'">'. $this->vehicle->profile->phone2 . ') </a>'
                : ''
            ;
            return $return;
    }

    public function getClientInfoWithoutPhone()
    {

        $return = '';
        if ($this->profile) {
            $return .= $this->profile->fioFull
                . '<br>';
        }
        if($this->id_company) $return .= $this->company->name . '<br>';

        return $return;
    }

    public function getPaymentText($withIconDiscount = true){
        return ($withIconDiscount)
                    ? TypePayment::findOne($this->type_payment)->textWithIconDiscount
                    : TypePayment::findOne($this->type_payment)->type
            ;
    }

    public function getPaymentMinText($withIconDiscount = true){
        return ($withIconDiscount)
            ? TypePayment::findOne($this->type_payment)->minTextWithIconDiscount
            : TypePayment::findOne($this->type_payment)->min_text
            ;
    }

    public function getPriceZonesWithInfo(){
        $return = '';
        foreach ($this->priceZones as $priceZone){
            $return .= $priceZone->getTextWithShowMessageButton($this->route->distance);
        }
        return $return;
    }

    public function setEventChangeStatusToExpired(){
        return
            Yii::$app->db->createCommand
            (
                '
                CREATE EVENT IF NOT EXISTS cancel_order_'
                . $this->id .
                ' ON SCHEDULE AT ( STR_TO_DATE ("'
                .
                $this->valid_datetime
                . '", "%d.%m.%Y %H:%i"))
                    DO BEGIN
                    UPDATE Orders 
                    SET status = IF(status = '
                . Order::STATUS_NEW . ' OR status = '
                . Order::STATUS_IN_PROCCESSING . ', '
                . Order::STATUS_EXPIRED
                . ', status), 
                    FLAG_SEND_EMAIL_STATUS_EXPIRED = 0 '
                . ' WHERE id = '. $this->id
                . ';
                    UPDATE setting 
                    SET FLAG_EXPIRED_ORDER = 0;
                    END;'
            )
                ->query()
            ;
    }

    public function deleteEventChangeStatusToExpired(){
        return
            Yii::$app->db->createCommand('
                    DROP EVENT IF EXISTS cancel_order_' . $this->id . ';'
            ) -> query();
        ;
    }

    public function changeStatus($newStatus, $id_client, $id_vehicle = null, $changeFinishCosts = true){
        if(!$id_client) return false;
        $url_client = Url::to(['/order/client'], true);
        $url_vehicle = Url::to(['/order/vehicle'], true);
        $email_from = Yii::$app->params['robotEmail'];
        $email_client = User::findOne($id_client)->email;
        if($id_vehicle && $vehicle = Vehicle::findOne($id_vehicle)) {
            $email_vehicle = User::findOne($vehicle->id_user)->email;
        }
        $push_to_client = true;
        $email_to_client = true;
        $push_to_vehicle = false;
        $email_to_vehicle = false;
        $message_can_review_client = false;
        $message_can_review_vehicle = false;
        //айдишники в сообщении клиенту, отзыв от и отзыв кому
        $client_id_to_review = null;
        $client_id_from_review = null;
        $event_review = null;
        //айдишники в сообщении водителю, отзыв от и отзыв кому
        $vehicle_id_to_review = null;
        $vehicle_id_from_review = null;

        $title_client = '';
        $title_vehicle = '';
        $message_client = '';
        $message_vehicle = '';
        $message_push_client = '';
        $message_push_vehicle = '';

        switch ($newStatus){
            case self::STATUS_IN_PROCCESSING:
                if(self::STATUS_VEHICLE_ASSIGNED){
                    $valid_datetime = $this->valid_datetime;
                    if(strtotime($this->valid_datetime) < time()) {
                        $valid_datetime = date('d.m.Y H:i', time() + (60 * 60));
                    }
                    $title_client = 'Заказ №'.$this->id.' в процессе поиска ТС.';
                    $title_vehicle = 'Заказ №'.$this->id.'. Вы отказались от заказа';
                    $message_vehicle = 'Вы отказались от ранее принятого заказа. <br>'
//                        .'ТС: ' .$vehicle->brandAndNumber . '<br>'
                        . $this->getFullNewInfo(false, true, false, false) . '<br>'
                        .'. <br> Клиент при желании может оценить Ваше действие, что повлияет на Ваш рейтинг водителя.'
                    ;
                    $message_push_vehicle = 'Вы отказались от заказа. Предупредите Клиента, что по заказу идет поиск другого ТС!';
                    $message_client = 'Водитель (ТC: '. $vehicle->brandAndNumber .') отказался от заказа.<br> Поиск ТС продолжится до '
                        . $valid_datetime
                        . '<br>. Вы можете оценить действия водителя в Личном кабинете, в разделе Уведомления. <br>'
                        . $this->getFullNewInfo(true, false, true, false) . '<br>';
                    $message_push_client = 'Водитель отказался от заказа. Поиск ТС продолжится!';
                    $push_to_vehicle = true;
                    $email_to_vehicle = true;

                   $message_can_review_client = true;
                   $client_id_to_review = $vehicle->user->id;
                   $client_id_from_review = $id_client;
                   $event_review = Review::EVENT_ORDER_CANCELED;

                    $this->valid_datetime = $valid_datetime;
                    $this->id_vehicle = null;
                    $this->id_car_owner = null;
                    $this->id_driver = null;
                    $this->id_pricezone_for_vehicle = null;
                    $this->discount = $this->getDiscount($this->id_user);
                    $this->setEventChangeStatusToExpired();
                    break;
                }

                break;
            case self::STATUS_NEW:
                switch ($this->status) {
                    case self::STATUS_NEW: case self::STATUS_IN_PROCCESSING:
                        $title_client = 'Заказ №' . $this->id . ' изменен.';
                        $this->deleteEventChangeStatusToExpired();
                        $this->setEventChangeStatusToExpired();

                    break;
                    case self::STATUS_CANCELED: case self::STATUS_EXPIRED:
                        $title_client = 'Заказ №' . $this->id . ' изменен и добавлен в поиск.';
                        $this->deleteEventChangeStatusToExpired();
                        $this->discount = $this->getDiscount($this->id_user);
                        $this->setEventChangeStatusToExpired();

                    break;
                }
                $message_client = 'Спасибо за Ваш заказ.  <br>'
                    . $this->getFullNewInfo(true, false, true, false);
                $message_push_client = 'Спасибо за Ваш заказ.';
                $this->discount = $this->getDiscount($this->id_user);
                break;
            case self::STATUS_VEHICLE_ASSIGNED:
                $vehicle = $this->vehicle;
                if($this->re){
                    $email_to_client = false;
                    $push_to_client = false;
                    $title_vehicle = 'Повторный заказ №'.$this->id.' зарегистрирован.';
                    $message_vehicle = $this->getFullNewInfo(false, true, false, false);
                    $email_to_vehicle = true;
                    $push_to_vehicle = true;
                    $this->datetime_access = date('d.m.Y H:i');
                    break;
                }
                $title_client = 'Заказ №'.$this->id.' принят водителем. ';
                $title_vehicle = 'Вы приняли заказ №'.$this->id.'.';
                $message_vehicle = 'Вы приняли заказ №'
                    . $this->id  . '<br>'
                    . $this->getFullInfoAboutVehicle(true, true, true, false) . '<br>'
                    . $this->getFullNewInfo(false, true, false, false)
                    . '<br><strong>Телефоны клиента в разделе Заказы на вкладке "В процессе...". </strong><br>';
                $message_push_vehicle = '';
                $message_client = $this->getFullInfoAboutVehicle(false, false, false)
                    . '<br><strong>Телефоны, паспорт и ВУ водителя в разделе Заказы на вкладке "В процессе...". </strong><br><br>'
                    . $this->getFullNewInfo(true,true, false, false);
                $message_push_client = $vehicle->brandAndNumber;
                $email_to_vehicle = true;
                $push_to_vehicle = true;
                $this->id_car_owner = $vehicle->user->id;
                $this->discount = $this->getDiscount($id_client);
                $this->datetime_access = date('d.m.Y H:i');
                $this->deleteEventChangeStatusToExpired();

                break;
            case self::STATUS_EXPIRED:
                $title_client = 'Заказ №' . $this->id . '. Машина не найдена.';
                $message_client = 'Машина не найдена. Заказ №' . $this->id . '. Вы можете повторить поиск 
                    в разделе "Заказы" на вкладке "Отмененные".';

                $this->FLAG_SEND_EMAIL_STATUS_EXPIRED = 1;
                break;
            case self::STATUS_CONFIRMED_VEHICLE:
                $title_client = 'Заказ №'.$this->id.' выполнен.';
                $title_vehicle = 'Заказ №'.$this->id.'. Вы подтвердили выполнение заказа.';
                $message_vehicle = $this->CalculateAndPrintFinishCost(false, true)['text'];
                $message_push_vehicle = 'Спасибо!';
                $message_client = $this->CalculateAndPrintFinishCost(false, false, true)['text'];
                $message_push_client = 'Спасибо, что Вы с нами!';
                $email_to_vehicle = true;
                $push_to_vehicle = true;

                $message_can_review_client = true;
                $message_can_review_vehicle = true;

                $client_id_to_review = $vehicle->user->id;
                $client_id_from_review = $id_client;
                $vehicle_id_to_review = $id_client;
                $vehicle_id_from_review = $vehicle->user->id;
                $event_review = Review::EVENT_ORDER_COMPLETED;

                if($this->type_payment == Payment::TYPE_CASH)$this->paid_status = self::PAID_YES;
                else {
                    if($this->paid_status == null) {
                        $this->paid_status = self::PAID_NO;
                    }
                }
                if($changeFinishCosts) {
                    $this->cost_finish = $this->getFinishCost(false);
                    $this->cost_finish_vehicle = $this->finishCostForVehicle;
                } else {
                    $message_vehicle = 'Итого к оплате: ' . $this->cost_finish_vehicle . ' р.';
                    $message_client = 'Итого к оплате: ' . $this->cost_finish . ' р.';
                }

                $finishContacts = $this->finishContacts;
                if(!$finishContacts) {
                    $finishContacts = new OrdersFinishContacts();
                    $finishContacts->id_order = $this->id;
                }
                $client = Profile::findOne($id_client);
                if($client && $this->id_user != $this->id_car_owner) {
                    $finishContacts->client_surname = $client->surname;
                    $finishContacts->client_name = $client->name;
                    $finishContacts->client_phone = $client->phone;
                }
                $car_owner = $this->carOwner;
                if($car_owner){
                    $finishContacts->car_owner_surname = $car_owner->surname;
                    $finishContacts->car_owner_name = $car_owner->name;
                    $finishContacts->car_owner_phone = $car_owner->phone;
                }
                $driver = $this->driver;
                if($driver){
                    $finishContacts->driver_surname = $driver->surname;
                    $finishContacts->driver_name = $driver->name;
                    $finishContacts->driver_phone = $driver->phone;
                }
                if($vehicle){
                    $reg_lic = $vehicle->regLicense;
                    if($reg_lic) {
                        $finishContacts->vehicle_brand = $reg_lic->brand;
                        $finishContacts->vehicle_number = $reg_lic->reg_number;
                    }
                }
                $finishContacts->save();

                if($this->re){
                    if($this->id_user == $this->id_car_owner){
                        $email_to_client = false;
                        $push_to_client = false;
                        $message_can_review_client = false;
                        $message_can_review_vehicle = false;
                        $title_vehicle = 'Заказ №'.$this->id.'. Вы подтвердили выполнение повторного заказа.';
                        $message_vehicle = $this->CalculateAndPrintFinishCost(false, true)['text'];
                    }
                }
                break;

            case self::STATUS_CANCELED:
                if($this->status == self::STATUS_NEW || $this->status == self::STATUS_IN_PROCCESSING){
                    $title_client = 'Заказ №'.$this->id.' отменен.';
                    $message_client = 'Вы отменили Ваш заказ.  <br>'
                        . $this->getFullNewInfo(true, false, true, false);
                    $message_push_client = 'Вы отменили Ваш заказ.';
                    $this->deleteEventChangeStatusToExpired();
                }
                if($this->status == self::STATUS_VEHICLE_ASSIGNED){
                    if(strtotime($this->valid_datetime) < time()) {

                    } else {

                    }
                    $title_client = 'Заказ №'.$this->id.'. Вы отменили заказ.';
                    $title_vehicle = 'Заказ №'.$this->id.' отменен клиентом.';
                    $message_vehicle = 'Клиент отменил принятый Вами заказ на <br>'
                        .'ТС: ' .$vehicle->brandAndNumber . '<br>'
                        . $this->getFullNewInfo(false, true, false, false) . '<br>'
                        .'. <br> При желании Вы можете оценить действие Клиента, что повлияет на его рейтинг клиента.'
                    ;
                    $message_push_vehicle = '';
                    $message_client = 'Пожалуйста, позвоните водителю и сообщите об отмене заказа <br>'
                        . $this->vehicleFioAndPhone . '<br>'
                        . $this->getFullNewInfo(true, false, true, false) . '<br>';
                    $message_push_client = 'Заказ №'.$this->id.'. Вы отменили заказ. Сообщите пожалуйста водителю об отмене!';
                    $push_to_vehicle = true;
                    $email_to_vehicle = true;

                    $message_can_review_vehicle = true;
                    $vehicle_id_to_review = $id_client;
                    $vehicle_id_from_review = $vehicle->user->id;
                    $event_review = Review::EVENT_ORDER_CANCELED;

                    if($this->id_user == $this->id_car_owner){
                        $title_vehicle = 'Вы удалили повторный Заказ №'.$this->id.'.';
                        $message_vehicle = '';
                        ;
                        $email_to_client = false;
                        $push_to_client = false;
                        $this->id_user = null;
                        $message_can_review_vehicle = false;
                        $message_can_review_client = false;
                    }
                    $this->id_vehicle = null;
                    $this->id_car_owner = null;
                    $this->id_driver = null;
                    $this->id_pricezone_for_vehicle = null;
                    $this->datetime_access = null;
                    break;
                }
                break;
            case self::STATUS_DISPUTE:

                break;
            case self::STATUS_NOT_ACCEPTED:
                $title = 'Заказ №' . $this->id . ' не принят в обработку.';
                $email_addresses [] = User::findOne($id_client)->email;
                functions::setFlashSuccess('Заказ аннулирован.');
                break;
        }
        // Емэил Клиенту
        if($email_to_client) {
            functions::sendEmail(
                $email_client,
                $email_from,
                $title_client,
                [
                    'text' => $message_client
                ],
                [
                    'html' => 'views/Order/change_status_html',
                    'text' => 'views/Order/change_status_text'
                ]
            );
        }
        //Емэил Водителю
        if($id_vehicle && $email_to_vehicle) {
            functions::sendEmail(
                $email_vehicle,
//                Yii::$app->params['robotEmail'],
                $email_from,
                $title_vehicle,
                [
                    'text' => $message_vehicle
                ],
                [
                    'html' => 'views/Order/change_status_html',
                    'text' => 'views/Order/change_status_text'
                ]
            );
        }

        //Сообщение клиенту
        if ($id_client && $push_to_client) {
            $Message_to_client = new Message([
                'id_to_user' => $id_client,
                'title' => $title_client,
                'text' => $message_client,
                'text_push' => $message_push_client,
                'url' => $url_client,
                'push_status' => Message::STATUS_NEED_TO_SEND,
                'email_status' => Message::STATUS_NEED_TO_SEND,
                'can_review_client' => $message_can_review_client,
                'can_review_vehicle' => false,
                'id_order' => $this->id,
                'id_to_review' => $client_id_to_review,
                'id_from_review' => $client_id_from_review
            ]);
            $Message_to_client->sendPush();
        }

        if ($message_can_review_client) {
            $review_client = new Review([
                'id_order' => $this->id,
                'id_message' => $Message_to_client->id,
                'event' => $event_review,
                'type' => Review::TYPE_TO_VEHICLE,
                'id_user_from' => $id_client,
                'id_user_to' => $vehicle->user->id,
            ]);
            $review_client->save(false);
        }

        //Сообщение водителю
        if($id_vehicle && $push_to_vehicle) {
            $Message_to_vehicle = new Message([
                'id_to_user' => $vehicle->id_user,
                'title' => $title_vehicle,
                'text' => $message_vehicle,
                'text_push' => $message_push_vehicle,
                'url' => $url_vehicle,
                'push_status' => Message::STATUS_NEED_TO_SEND,
                'email_status' => Message::STATUS_NEED_TO_SEND,
                'can_review_client' => false,
                'can_review_vehicle' => $message_can_review_vehicle,
                'id_order' => $this->id,
                'id_to_review' => $vehicle_id_to_review,
                'id_from_review' => $vehicle_id_from_review
            ]);
            $Message_to_vehicle->sendPush();
        }

        if($message_can_review_vehicle){
            $review_vehicle = new Review([
                'id_order' => $this->id,
                'id_message' => $Message_to_vehicle->id,
                'event' => $event_review,
                'type' => Review::TYPE_TO_CLIENT,
                'id_user_from' => $vehicle->user->id,
                'id_user_to' => $id_client,
            ]);
            $review_vehicle->save(false);
        }

        $this->scenario = self::SCENARIO_UPDATE_STATUS;
        $this->status = $newStatus;
        $this->update_at = date('d.m.Y H:i', time());

        if($this->save()){
            emails::sendToAdminChangeOrder($this->id);
            functions::setFlashSuccess('Статус заказа №' . $this->id . ' изменен на "' . $this->statusText . '".');
            return true;
        } else {
            functions::setFlashWarning('Ошибка на сервере!');
            return false;
        }
    }

    public function getFullNewInfo($showClientPhone = false, $showPriceForVehicle = false, $showPriceZones = true, $html = true){
        $return = 'Заказ №' . $this->id . ' на ' .  $this->datetime_start .'<br>';
        $return .= 'Маршрут: ' . $this->route->fullRoute . '<br>';
        $return .= $this->getShortInfoForClient(true) . ' <br>';
        $return .= ($showPriceForVehicle) ? 'Тарифная зона №' . $this->getNumberPriceZoneForVehicle($html) . '. <br>' : '';
        $return .= ($showPriceZones) ? 'Тарифные зоны: ' . $this->idsPriceZones . '. <br>' :'';
        $return .= 'Тип оплаты: ' . $this->paymentText . '. <br>';
        if($this->id_user != $this->id_car_owner) {
            $return .= ($showClientPhone)
                ? 'Заказчик:' . $this->getClientInfo($html) . ' <br>'
                : 'Заказчик:' . $this->clientInfoWithoutPhone . ' <br>';
        } else {
            $return .= '"Повторный заказ" <br>' . $this->comment;
        }
        return $return;
    }

    public function getFullFinishInfo($showClientPhone = false, $realRoute = null, $html = true, $finish = false){
        if($this->realRoute) $real_route = $this->realRoute;
        else {
            if(!$realRoute) return false;
            $real_route = $realRoute;
        }
        $return = 'Заказ №' . $this->id;
        $return .= '<br>Время выезда: ' .  $this->real_datetime_start .'<br>';
        $return .= 'Время возвращения: ' .  $this->datetime_finish .'<br>';
        $return .= 'Маршрут: ' . $real_route->fullRoute ;
        if($this->real_km){
            $return .= 'Фактический пробег: ' . $this->real_km . ' км <br>';
        }
        $return .= $this->getShortInfoForClient(true, true) . ' <br>';
        $return .= 'Тарифная зона №' . $this->getNumberPriceZoneReal($html) . '. <br>';
        $return .= 'Тип оплаты: ' . $this->paymentText . '. <br>';
        if($this->id_user != $this->id_car_owner) {
            $return .= ($showClientPhone)
                ?'Заказчик:' . $this->clientInfo . ' <br>'
                :'Заказчик:' . $this->clientInfoWithoutPhone . ' <br>';
        } else {
            $return .= '"Повторный заказ" <br>' . $this->comment;
        }
        return $return;
    }

    public function setScenarioForUpdate(){
        switch ($this->id_vehicle_type){
            case Vehicle::TYPE_TRUCK:
                $this->scenario = self::SCENARIO_UPDATE_TRUCK;
                break;
            case Vehicle::TYPE_PASSENGER:
                $this->scenario = self::SCENARIO_UPDATE_PASS;
                break;
            case Vehicle::TYPE_SPEC:
                $tmpBodyTypies = $this->body_typies[0];
                switch ($tmpBodyTypies){
                    case Vehicle::BODY_manipulator:
                        $this->scenario = self::SCENARIO_UPDATE_MANIPULATOR;
                        break;
                    case Vehicle::BODY_dump:
                        $this->scenario = self::SCENARIO_UPDATE_DUMP;
                        break;
                    case Vehicle::BODY_crane:
                        $this->scenario = self::SCENARIO_UPDATE_CRANE;
                        break;
                    case Vehicle::BODY_excavator:
                        $this->scenario = self::SCENARIO_UPDATE_EXCAVATOR;
                        break;
                    case Vehicle::BODY_excavator_loader:
                        $this->scenario = self::SCENARIO_UPDATE_EXCAVATOR;
                        break;
                }
                break;
        }

    }

    public function setScenarioForFinish(){
        switch ($this->id_vehicle_type){
            case Vehicle::TYPE_TRUCK:
                $this->scenario = self::SCENARIO_FINISH_TRUCK;
                break;
            case Vehicle::TYPE_PASSENGER:
                $this->scenario = self::SCENARIO_FINISH_PASS;
                break;
            case Vehicle::TYPE_SPEC:
//                $tmpBodyTypies = $this->body_typies[0];
//                switch ($tmpBodyTypies){
                switch ($this->vehicle->body_type){
                    case Vehicle::BODY_manipulator:
                        $this->scenario = self::SCENARIO_FINISH_MANIPULATOR;
                        break;
                    case Vehicle::BODY_dump:
                        $this->scenario = self::SCENARIO_FINISH_DUMP;
                        break;
                    case Vehicle::BODY_crane:
                        $this->scenario = self::SCENARIO_FINISH_CRANE;
                        break;
                    case Vehicle::BODY_excavator:
                        $this->scenario = self::SCENARIO_FINISH_EXCAVATOR;
                        break;
                    case Vehicle::BODY_excavator_loader:
                        $this->scenario = self::SCENARIO_FINISH_EXCAVATOR;
                        break;
                }
                break;
        }

    }

    public function getFullInfoAboutVehicle($showPhones = true, $showPassport = true, $showDriveLicence = true, $html = false){
        $return = '';
        if($FC = $this->finishContacts){
            $return .= $FC->getVehicleInfo() . '<br>'
                . $FC->getCarOwnerInfo() . '<br>'
                . $FC->getDriverInfo() . '<br>';
            return $return;
        }
        if(!$this->id_vehicle) return false;
        $vehicle = $this->vehicle;
        if(!$vehicle) return false;
        $vehicle_user = $this->vehicle->profile;
        if($this->id_driver) $driver = Driver::findOne($this->id_driver);
        $return .= $vehicle->fullInfo . '<br>';
        if($this->id_driver){
            if($driver) $return .= 'Водитель: ' . $driver->getFullInfo($showPhones, $showPassport, $showDriveLicence);
            $return .= 'Владелец ТС: ' . $vehicle_user->getProfileInfo($showPhones);
        } else {
            if($vehicle_user) $return .= 'Водитель: ' . $vehicle_user->getDriverFullInfo($showPhones, $showPassport, $showDriveLicence, $html);
        }
        return $return;
    }

    public function getArrayAttributesForSetFinishPricezone(){
        $body_type = Vehicle::findOne($this->id_vehicle)->bodyType;
        $res = [];
        switch ($this->id_vehicle_type){
            case Vehicle::TYPE_TRUCK;
                $res = ['real_tonnage', 'real_length', 'real_volume'];
                break;
            case Vehicle::TYPE_PASSENGER:
                $res = ['real_passengers'];
                break;
            case Vehicle::TYPE_SPEC:
                switch ($body_type->id){
                    case Vehicle::BODY_manipulator:
                        $res = ['real_tonnage','real_length', 'real_tonnage_spec', 'real_length_spec'];
                        break;
                    case Vehicle::BODY_dump:
                        $res = ['real_tonnage', 'real_volume'];
                        break;
                    case Vehicle::BODY_crane:
                        $res = ['real_tonnage_spec', 'real_length_spec'];
                        break;
                    case Vehicle::BODY_excavator:
                        $res = ['real_volume_spec'];
                        break;
                    case Vehicle::BODY_excavator_loader:
                        $res = ['real_volume_spec'];
                        break;
                }
                break;
        }

        return $res;
    }

    public function getArrayAttributesForReCreate(){
        $vehicle = Vehicle::findOne($this->id_vehicle);
        if(!$vehicle) return false;
        $body_type = $vehicle->bodyType;
        $res = [];
        switch ($this->id_vehicle_type){
            case Vehicle::TYPE_TRUCK;
                $res = ['tonnage', 'length', 'volume'];
                break;
            case Vehicle::TYPE_PASSENGER:
                $res = ['passengers'];
                break;
            case Vehicle::TYPE_SPEC:
                switch ($body_type->id){
                    case Vehicle::BODY_manipulator:
                        $res = ['tonnage','length', 'tonnage_spec', 'length_spec'];
                        break;
                    case Vehicle::BODY_dump:
                        $res = ['tonnage', 'volume'];
                        break;
                    case Vehicle::BODY_crane:
                        $res = ['tonnage_spec', 'length_spec'];
                        break;
                    case Vehicle::BODY_excavator: case Vehicle::BODY_excavator_loader:
                        $res = ['volume_spec'];
                        break;
                }
                break;
        }
        return $res;
    }

    public function copyValueToRealValue(){
        if(!$this) return false;
        if(!$this->vehicle) return false;
        $body_type = $this->vehicle->bodyType->id;
        switch ($this->id_vehicle_type){
            case Vehicle::TYPE_TRUCK:
                $this->real_tonnage = $this->tonnage;
                $this->real_length = $this->length;
                $this->real_volume = $this->volume;
                $this->real_longlength = $this->longlength;
                break;
            case Vehicle::TYPE_PASSENGER:
                $this->real_passengers = $this->passengers;
                break;
            case Vehicle::TYPE_SPEC:
                switch ($body_type){
                    case Vehicle::BODY_manipulator:
                        $this->real_tonnage = $this->tonnage;
                        $this->real_length = $this->length;
                        $this->real_tonnage_spec = $this->tonnage_spec;
                        $this->real_length_spec = $this->length_spec;
                        break;
                    case Vehicle::BODY_dump:
                        $this->real_tonnage = $this->tonnage;
                        $this->real_volume = $this->volume;
                        break;
                    case Vehicle::BODY_crane:
                        $this->real_tonnage_spec = $this->tonnage_spec;
                        $this->real_length_spec = $this->length_spec;
                        break;
                    case Vehicle::BODY_excavator: case Vehicle::BODY_excavator_loader:
                        $this->real_volume_spec = $this->real_volume_spec;
                        break;
                }
                break;
        }
    }

    public function CalculateAndPrintFinishCost(bool $html = true, bool $forVehicle = false, $withDiscount = false) : array {
        $distance = $this->real_km;
        $hour = $this->real_h;
        $real_pz = PriceZone::findOne(['unique_index' => $this->id_price_zone_real]);
        if(!$real_pz || !$distance) return ['text' => 'Ошибка на сервере', 'cost' => 0];
        if($forVehicle) $real_pz = $real_pz->getWithDiscount(self::getVehicleProcentPrice());
        if(!$real_pz || !$distance) return ['text' => 'Ошибка на сервере', 'cost' => 0];
        if($distance){
            $cost = 0;
            $text = ($html)
                ? $real_pz->getTextWithShowMessageButton($this->real_km)
                : 'Тарифная зона №' . $real_pz->id;
            if($distance < 20){
                $min_cost = $real_pz->min_r_10 * $real_pz->r_h;
                $text .= '<br>Мин. оплата при пробеге менее 20км: ' . $real_pz->min_r_10 . 'ч. ';
            }
            if ($distance >= 20 && $distance<40){
                $min_cost = $real_pz->min_r_20 * $real_pz->r_h;
                $text .= '<br>Мин. оплата при пробеге от 20км до 39км: ' . $real_pz->min_r_20 . 'ч. ';
            }
            if ($distance >= 40 && $distance<60){
                $min_cost = $real_pz->min_r_30 * $real_pz->r_h;
                $text .= '<br>Мин. оплата при пробеге от 40км до 59км: ' . $real_pz->min_r_30 . 'ч. ';
            }
            if ($distance >= 60 && $distance<80){
                $min_cost = $real_pz->min_r_40 * $real_pz->r_h;
                $text .= '<br>Мин. оплата при пробеге от 60км до 79км: ' . $real_pz->min_r_40 . 'ч. ';
            }
            if ($distance >= 80 && $distance<120){
                $min_cost = $real_pz->min_r_50 * $real_pz->r_h;
                $text .= '<br>Мин. оплата при пробеге от 80км до 119км: ' . $real_pz->min_r_50 . 'ч. ';
            }

            if ($distance >= 120) {
                $text .= '<br>Фактический пробег: ' . $this->real_km . ' km. (более 120 км). ';
                if ($this->id_vehicle_type == Vehicle::TYPE_SPEC && !$real_pz->r_km) {
                    $cost = 0;
                    $text .= '<br>Для данного типа ТС действует договорная стоимость между Водителем и Клиентом';
                } else {
                    $min_cost = $real_pz->min_price;
                    $real_cost = ($real_pz->r_km * $this->real_km);
                    $text .= '<br>Стоимость 1 км: ' . $real_pz->r_km . 'р. ';
//                    $text .= '<br>Фактический пробег: ' . $this->real_km . 'км. ';

                    $text .= '<br>Итого за пробег: ' . $real_cost . 'р. ';
                    $text .= '<br>Время потраченное на погрузку/разгрузку/ожидание: ' . $this->real_h_loading . 'ч. ';
                    $text .= '<br>Бесплатное время на погрузку/разгрузку/ожидание: ' . $real_pz->h_loading . 'ч. ';
                    if($this->real_h_loading && ($this->real_h_loading - $real_pz->h_loading)>0){
                        $real_cost += ($this->real_h_loading - $real_pz->h_loading) * $real_pz->r_loading;
                        $text .= '<br>Стоимость часа (сверх бесплатных) на погрузку/разгрузку/ожидание: ' . $real_pz->r_loading . 'р. ';
                        $text .= '<br>Итого за лишнее время на погрузку/разгрузку/ожидание: ';
                        $text .=  $real_pz->r_loading * ($this->real_h_loading - $real_pz->h_loading);
                        $text .= 'р. ';
                    }
                    if($this->real_remove_awning && $real_pz->remove_awning) {
                        $text .= '<br>Количество "растентовок" сверху или сбоку: ' . $this->real_remove_awning . '. ';
                        $text .= '<br>Стоимость одной "растентовки" одной стороны: ' . $real_pz->remove_awning . 'р. ';
                        $real_cost += $this->real_remove_awning * $real_pz->remove_awning;
                        $text .= '<br>Итого за "растентовку": ' . $this->real_remove_awning * $real_pz->remove_awning;
                    }

                    if($min_cost > $real_cost){
                        $cost = $min_cost;
                        $text .= '<br>Минимальная оплата при пробеге более 120км.: ' . $real_pz->min_price;
                    } else {
                        $cost = $real_cost;
                    }
//                    $text .= '<br>Время потраченное на погрузку/разгрузку/ожидание: ' . $this->real_h_loading . 'ч. ';
//                    $text .= '<br>Бесплатное время на погрузку/разгрузку/ожидание: ' . $real_pz->h_loading . 'ч. ';
//                    $text .= '<br>Стоимость часа (сверх бесплатных) на погрузку/разгрузку/ожидание: ' . $real_pz->r_loading . 'р. ';
//                    $text .= '<br>Итого за лишнее время на погрузку/разгрузку/ожидание: ';
//                    $text .=  (($this->real_h_loading - $real_pz->h_loading)>0) ? $real_pz->h_loading * ($this->real_h_loading - $real_pz->h_loading) : '0';
//                    $text .= 'р. ';
//                    if($this->additional_cost){
//                        $cost += $this->additional_cost;
//                        $text .= '<br>Дополнительные расходы: ' . $this->additional_cost . 'Р. ';
//                    }
                }
            } else {
                $real_cost = $this->real_h * $real_pz->r_h;
                ($min_cost > $real_cost)? $cost = $min_cost : $cost = $real_cost;
                $text .= '<br>Время работы (с учетом дороги от/до г.Обнинск): ' . $this->real_h . 'ч. ';
                $text .= '<br>Стоимость 1 часа: ' . $real_pz->r_h . 'р. ';

//                if($this->additional_cost){
//                    $cost += $this->additional_cost;
//                    $text .= '<br>Дополнительные расходы: ' . $this->additional_cost . 'Р. ';
//                }
            }

            $text .= '<br>Комментарии водителя: ' . $this->comment_vehicle;
            if($withDiscount) {
                $cost = $this->getFinishCost(false);
                if($this->additional_cost){
                    $cost += $this->additional_cost;
                    $text .= '<br>Дополнительные расходы: ' . $this->additional_cost . 'Р. ';
                }
                if(!$forVehicle && $this->discount) {
                    $text .= '<br>Скидка: ' . $this->discount;
                    $text .= ($html) ? Html::img('/img/icons/discount-16.png', ['title' => 'Действует скидка!']) : '%';
                }
            } else {
                if($this->additional_cost){
                    $cost += $this->additional_cost + ($this->additional_cost * 10 / 100);
                    if($forVehicle){
                        $text .= '<br>Дополнительные расходы: ' . $this->additional_cost . 'Р. ';
                    } else {
                        $text .= '<br>Дополнительные расходы: ' . ($this->additional_cost + ($this->additional_cost * 10 / 100))
                            . 'Р. ';
                    }
                }
            }

            $text .= '<br>Тип оплаты: ' . $this->getPaymentText(false);

            $cost = round($cost);
            $return['cost'] = $cost;
            if($return['cost']) {
                if($forVehicle) {
                    if ($this->additional_cost) {
                        $text .= '<br><br><strong>Итого к оплате '
                            . ($return['cost'] - ($this->additional_cost * 10 / 100))
                            . ' руб.</strong>';
                    } else {
                        $text .= '<br><br><strong>Итого к оплате ' . $return['cost'] . ' руб.</strong>';
                    }
                } else {
                    $text .= '<br><br><strong>Итого к оплате ' . $this->getFinishCost() . ' руб.</strong>';
                }
            }
            $return['text'] =  $text;
            $return['cost'] = round($return['cost']);

            return $return;
        }
        return ['text' => 'error', 'cost' => 0];
    }

    public function getReal_h(){
        if(!$this->real_datetime_start || ! $this->datetime_finish) return false;
        return ceil((strtotime($this->datetime_finish) - strtotime($this->real_datetime_start))/3600/0.5)*0.5;
    }

    public function getFinishCost($html = true){
        if(!$this->cost) return false;
        if($html){
            return ($this->discount)
                ? '<s>' . $this->cost . '</s> '
                . '<strong> '
                . round($this->cost - (($this->cost - intval($this->additional_cost)) * $this->discount/100))
                . '</strong>'
                : $this->cost;
        } else{
            return ($this->discount)
                ? round($this->cost - (($this->cost - intval($this->additional_cost)) * $this->discount/100))
                : $this->cost;
        }
    }

    public function getFinishCostForVehicle(){
        if(!$this->cost) return false;
        return round($this->cost - ($this->cost * $this->getVehicleProcentPrice()/100));
//        return round($this->CalculateAndPrintFinishCost(false, true, false)['cost']);
    }

    public function getVehicleProcentPrice(){
        return \app\models\setting\SettingVehicle::find()->limit(1)->one()->price_for_vehicle_procent;
    }

    public function getArrayPaidStatuses(){
        return [
            self::PAID_YES => 'Оплачен',
            self::PAID_NO => 'Не оплачен',
            self::PAID_YES_AVANS => 'Частично оплачен'
        ];
    }

    public function getNumberPriceZoneForVehicle($html){
        $pricezone = PriceZone::findOne(['unique_index' => $this->id_pricezone_for_vehicle]);
        if($pricezone){
            return $pricezone->getPriceZoneForCarOwner($this->id_car_owner)->getIdWithButton($html);
        }
        return null;
    }

    public function getNumberPriceZoneReal($html){
        $pricezone = PriceZone::findOne(['unique_index' => $this->id_price_zone_real]);
        if($pricezone){
            return $pricezone->getPriceZoneForCarOwner($this->id_car_owner)->getIdWithButton($html);
        }
        return null;
    }

    public function PrintFinishCalculate($html = true, $forVehicle = false, $withDiscount = false){
        $return = '';
        if($this->cost){
            $return .= $this->CalculateAndPrintFinishCost($html, $forVehicle, $withDiscount)['text'];
        } else {
            $return .= 'Стоимость заказа: ';
            $return .= ($forVehicle) ? $this->cost_finish_vehicle : $this->cost_finish;
            $return .= ' руб.';
        }
        return $return;
    }

    public function changeVehicleByFinished($id_new_vehicle, $id_new_driver, $redirect = '/logist/order'){
        $finishContacts = $this->finishContacts;
        if(!$finishContacts) {
            $finishContacts = new OrdersFinishContacts();
            $finishContacts->id_order = $this->id;
        }
        $vehicle = Vehicle::findOne($id_new_vehicle);
        if(!$vehicle) {
            functions::setFlashWarning('Такого ТС не существует.');
            return false;
        }

        $client = Profile::findOne($this->id_user);
        if($client && $this->id_user != $this->id_car_owner) {
            $finishContacts->client_surname = $client->surname;
            $finishContacts->client_name = $client->name;
            $finishContacts->client_phone = $client->phone;
        }

        $this->id_car_owner = $vehicle->id_user;
        $car_owner = $this->carOwner;
        if(!$car_owner){
            functions::setFlashWarning('Такого автовладельца не существует.');
            return false;
        }
        if($car_owner){
            $finishContacts->car_owner_surname = $car_owner->surname;
            $finishContacts->car_owner_name = $car_owner->name;
            $finishContacts->car_owner_phone = $car_owner->phone;
        }
        $driver = Driver::findOne($id_new_driver);
        if($driver){
            $finishContacts->driver_surname = $driver->surname;
            $finishContacts->driver_name = $driver->name;
            $finishContacts->driver_phone = $driver->phone;
        }
        if($vehicle){
            $reg_lic = $vehicle->regLicense;
            if($reg_lic) {
                $finishContacts->vehicle_brand = $reg_lic->brand;
                $finishContacts->vehicle_number = $reg_lic->reg_number;
            }
        }
//        $finishContacts->save();

        return $finishContacts;
    }

    public function getInfoFinance(){
        $return = '';
        switch ($this->status){
            case self::STATUS_CONFIRMED_VEHICLE:case self::STATUS_CONFIRMED_CLIENT: case self::STATUS_CONFIRMED_SET_SUM:
                $realRoute = $this->realRoute;
                $driver = $this->driver;
                $carOwner = $this->carOwner;
                $vehicle = $this->vehicle;
                if($realRoute){
                    $return .= $realRoute->getFullRoute(false);
                } else {
                    $route = $this->route;
                    if($route) $return .= $route->getFullRoute(false);
                }
                if($carOwner){
                    $return .= '<br><br>#' . $carOwner->id_user . ' ("' . $carOwner->old_id . '")';
                }
                if($driver){
                    $return .= '<br>' . $driver->fio;
                } else {
                    if($carOwner){
                        $return .= '<br>' . $carOwner->fioFull;
                    }
                }
                if($vehicle){
                    $regLicence = $vehicle->regLicense;
                    if($regLicence) {
                        $return .= '<br>'. $regLicence->brand . ' '. $regLicence->reg_number;
                    }
                }
                $return .= '<br><br>' . $this->comment_vehicle;
                break;
        }

        return $return;
    }

    public function getSuitableVehicles($forAlert = false){
//        $vehicles =[];
        $Vehicles = Vehicle::find()->where(['in', 'status', [Vehicle::STATUS_ACTIVE, Vehicle::STATUS_ONCHECKING]])
            ->orderBy('id_user')->all();
        if(!is_array($Vehicles)) return [];

        foreach ($Vehicles as $vehicle) {
            if (!$vehicle->canOrder($this)) {
                ArrayHelper::removeValue($Vehicles, $vehicle);
                continue;
            }
            if($forAlert && $vehicle->profile->email){
                $calendar = CalendarVehicle::find(['id_vehicle' => $vehicle->id])
                    ->andWhere(['date' => functions::DayToStartUnixTime($this->datetime_start)])
                    ->one();
                $calendar_status = CalendarVehicle::STATUS_FREE;
                if($calendar) $calendar_status = $calendar->status;
                    if($route = $this->route){
                        if($route->distance >= 120){
                            if($calendar_status != CalendarVehicle::STATUS_FREE
                                || $vehicle->hasOrderOnDate($this->datetime_start)
                            ){
                                ArrayHelper::removeValue($Vehicles, $vehicle);
                            }
                        }else{
                            if($calendar_status == CalendarVehicle::STATUS_BUSY
                                && $vehicle->hasOrderOnDate($this->datetime_start) < 120
                            ){
                                ArrayHelper::removeValue($Vehicles, $vehicle);
                            }
                        }
                    }
            }
        }
        return $Vehicles;
    }

    public function getSortSuitableVehicles($forAlert = false){
        $order = $this;
        $vehicles = $order->getSuitableVehicles($forAlert);
        if(!$vehicles) return false;

        if($vehicles) {
            usort($vehicles, function (Vehicle $a, Vehicle $b) use ($order) {
                if($a->getMinRate($order)->r_km > $b->getMinRate($order)->r_km) {
                    return 1;
                }
                if($a->getMinRate($order)->r_km < $b->getMinRate($order)->r_km) {
                    return -1;
                }
                if($a->getMinRate($order)->r_km == $b->getMinRate($order)->r_km) {
                    if(
                        ($a->user->canRole('vip_car_owner') && !$b->user->canRole('vip_car_owner'))
                        ||
                        ($a->user->canRole('car_owner') && $b->user->canRole('user'))
                    ){
                        return -1;
                    }

                    if(
                        ($a->user->canRole('vip_car_owner') && $b->user->canRole('vip_car_owner'))
                        ||
                        ($a->user->canRole('car_owner') && $b->user->canRole('car_owner'))
                        ||
                        ($a->user->canRole('user') && $b->user->canRole('user'))
                    ){
                        if ($order->type_payment == Payment::TYPE_BANK_TRANSFER) {
                            if($a->user->profile->balanceCarOwnerSum < $b->user->profile->balanceCarOwnerSum){return -1;}
                            if($a->user->profile->balanceCarOwnerSum > $b->user->profile->balanceCarOwnerSum){return 1;}
                            if($a->user->profile->balanceCarOwnerSum == $b->user->profile->balanceCarOwnerSum){return 0;}
                        } else {
                            if($a->user->profile->balanceCarOwnerSum < $b->user->profile->balanceCarOwnerSum){return 1;}
                            if($a->user->profile->balanceCarOwnerSum > $b->user->profile->balanceCarOwnerSum){return -1;}
                            if($a->user->profile->balanceCarOwnerSum == $b->user->profile->balanceCarOwnerSum){return 0;}
                        }
                    }
                    return 1;
                }
            });
        }
        return $vehicles;
    }

    public function getSortArrayCarOwnerIdsForFind($forAlert = true){
        $vehicles = $this->getSuitableVehicles($forAlert);
        if(!$vehicles) return false;
        $res = [];
        foreach ($vehicles as $item){
            $user = $item->user;
            if(!in_array($user->id, $res)){
                $res[] = $user->id;
            }
        }
        return $res;
    }

    public function sendMesAfterChangePaidStatus(){
        $client = $this->profile;
        $client = Profile::findOne(['id_user' => 186]);
        $car_owner = $this->carOwner;
        $car_owner = Profile::findOne(['id_user' => 186]);

        if($client && !$this->re){
            $message_client = new Message();
            $message_client->id_order = $this->id;
            $message_client->id_to_user = $client->id_user;
            $message_client->type = Message::TYPE_CHANGE_PAID_STATUS;
            $message_client->title = 'Заказ №' . $this->id . '. ' . $this->paidText . '.';
            $message_client->title = $client->email;
            $message_client->url = Url::to('/order/client', true);
            $message_client->text = 'Статус оплаты изменен на "' . $this->paidText . ' клиентом."';
            $message_client->text_push = 'Статус оплаты изменен на "' . $this->paidText . ' клиентом."';
            if($client->settings){
                if($client->settings->send_push && $client->user->push_ids){
                    $message_client->sendPush(false);
                }
            } else {
                if($client->user->push_ids){
                    $message_client->sendPush(false);
                }
            }
            $send_client_email = false;
            if($client->settings){
                if($client->settings->send_email && $client->email){
                    $send_client_email = true;
                }
            } else {
                if($client->email){
                    $send_client_email = true;
                }
            }
            if($send_client_email){
                $email = [];
                if($client->email) $email[] = $client->email;
                if($client->email2) $email[] = $client->email2;
                if(functions::sendEmail(
                    $email,
                    null,
                    $message_client->title,
                    [
                        'name' => $client->name,
                        'id_order' => $this->id,
                        'paid_status' => $this->paidText
                    ],
                    [
                        'html' => 'views/Order/change_paid_status_html',
                        'text' => 'views/Order/change_paid_status_text',
                    ]
                )){
                    $message_client->email_status = Message::STATUS_SEND;
                    $message_client->save();
                } else {
                    $message_client->email_status = Message::STATUS_NEED_TO_SEND;
                }
            }
        }

        if($car_owner){
            $message_car_owner = new Message();
            $message_car_owner->id_order = $this->id;
            $message_car_owner->id_to_user = $car_owner->id_user;
            $message_car_owner->type = Message::TYPE_CHANGE_PAID_STATUS;
            $message_car_owner->title = 'Заказ №' . $this->id . '. ' . $this->paidText . '.';
            $message_car_owner->url = Url::to('/order/client', true);
            $message_car_owner->text = 'Статус оплаты изменен на "' . $this->paidText . ' клиентом."';
            $message_car_owner->text_push = 'Статус оплаты изменен на "' . $this->paidText . ' клиентом."';
            if($car_owner->settings){
                if($car_owner->settings->send_push && $car_owner->user->push_ids){
                    $message_car_owner->sendPush(false);
                }
            } else {
                if($car_owner->user->push_ids){
                    $message_car_owner->sendPush(false);
                }
            }
            $send_car_owner_email = false;
            if($car_owner->settings){
                if($car_owner->settings->send_email && $car_owner->email){
                    $send_car_owner_email = true;
                }
            } else {
                if($car_owner->email){
                    $send_car_owner_email = true;
                }
            }
            if($send_car_owner_email){
                $email = [];
                if($car_owner->email) $email[] = $car_owner->email;
                if($car_owner->email2) $email[] = $car_owner->email2;
                if(functions::sendEmail(
                    $email,
                    null,
                    $message_car_owner->title,
                    [
                        'name' => $car_owner->name,
                        'id_order' => $this->id,
                        'paid_status' => $this->paidText
                    ],
                    [
                        'html' => 'views/Order/change_paid_status_html',
                        'text' => 'views/Order/change_paid_status_text',
                    ]
                )){
                    $message_car_owner->email_status = Message::STATUS_SEND;
                    $message_car_owner->save();
                } else {
                    $message_car_owner->email_status = Message::STATUS_NEED_TO_SEND;
                }
            }
        }

    }
 }


