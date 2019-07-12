<?php

namespace app\models;

use app\components\functions\functions;
use app\models\setting\Setting;
use app\models\setting\SettingVehicle;
use app\models\setting\SettingClient;
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

    public $body_typies;
    public $loading_typies;
    public $suitable_rates;
    public $selected_rates;
    public $ClientPhone;
    public $ClientPaidCash;

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
            [['id_vehicle_type','body_typies'], 'required', 'message' => 'Выберите один из вариантов.'],
            [['loading_typies'], 'validateLoadingTypies', 'skipOnEmpty' => false],
            ['tonnage', 'validateTonnage','skipOnEmpty' => false],
            [['selected_rates', 'type_payment'], 'required', 'message' => 'Выберите хотя бы один из вариантов'],
            [['datetime_start', 'valid_datetime', 'type_payment','datetime_finish', 'real_datetime_start', 'real_km'], 'required'],
            ['passengers', 'validatePassengers', 'skipOnEmpty' => false],
            [['id_company'],
//                'validateConfirmCompany', 'skipOnEmpty' => false
                    'safe'
            ],
            [['datetime_access', 'FLAG_SEND_EMAIL_STATUS_EXPIRED',
                'id_price_zone_for_vehicle', 'discount', 'cost_finish', 'cost_finish_vehicle',
                'ClientPhone', 'id_user'],
                'safe'
            ],
            [['additional_cost','ClientPaidCash'], 'default', 'value' => '0'],
            [['real_tonnage', 'real_length', 'real_volume', 'real_passengers', 'real_tonnage_spec',
                'real_length_spec', 'real_volume_spec', 'cost', 'id_review_vehicle', 'id_review_client'], 'number' ],
            [['suitable_rates', 'datetime_access', 'id_route', 'id_route_real', 'id_price_zone_real', 'cost', 'comment'], 'safe'],
            [['id','longlength', 'ep', 'rp', 'lp', 'id_route', 'id_route_real', 'additional_cost',
                'id_payment', 'status', 'type_payment', 'passengers', 'real_km', 'id_pricezone_for_vehicle','id_car_owner'], 'integer'],
            [['tonnage', 'length', 'width', 'height', 'volume', 'tonnage_spec', 'length_spec',
                'volume_spec'], 'number'],
            [['cargo', 'comment_vehicle'], 'string'],
            [['create_at', 'update_at'], 'default', 'value' => date('d.m.Y H:i')],
            ['status', 'default', 'value' => self::STATUS_NEW],
            ['paid_status', 'default', 'value' => self::PAID_NO],
            [['id_vehicle', 'id_driver'], 'required', 'message' => 'Выберите один из вариантов'],
            [['paid_status', 'paid_car_owner_status'], 'default', 'value' => self::PAID_NO],
            ['real_h_loading', 'default', 'value' => 0],
            ['real_remove_awning', 'default' , 'value' => 0],
            ['type_payment', 'validateForUser'],
            [['avans_client'], 'number']
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios(); // TODO: Change the autogenerated stub
        $scenarios[self::SCENARIO_UPDATE_STATUS] = ['status'];
        $scenarios[self::SCENARIO_ACCESSING] = ['id', 'id_vehicle', 'id_driver', 'id_car_owner'];
        $scenarios[self::SCENARIO_NEW_ORDER] = ['id_vehicle_type','body_typies', 'loading_typies',
            'tonnage', 'selected_rates', 'type_payment', 'datetime_start', 'valid_datetime',
            'passengers','id_company', 'status', 'create_at', 'update_at'];
        $scenarios[self::SCENARIO_LOGIST_NEW_ORDER] = ['id_vehicle_type','body_typies', 'loading_typies',
            'tonnage', 'selected_rates', 'type_payment', 'datetime_start', 'valid_datetime',
            'passengers', 'status', 'create_at', 'update_at'];
        $scenarios[self::SCENARIO_UPDATE_TRUCK] = [
            'body_typies', 'loading_typies', 'tonnage', 'selected_rates', 'type_payment',
            'datetime_start', 'valid_datetime', 'id_company', 'status', 'update_at'
        ];
        $scenarios[self::SCENARIO_UPDATE_PASS] = [
            'body_typies', 'passengers', 'cargo', 'selected_rates', 'type_payment',
            'datetime_start', 'valid_datetime', 'id_company', 'status', 'update_at'
        ];
        $scenarios[self::SCENARIO_UPDATE_MANIPULATOR] = [
            'tonnage', 'tonnage_spec', 'selected_rates', 'type_payment',
            'datetime_start', 'valid_datetime', 'id_company', 'status', 'update_at'
        ];
        $scenarios[self::SCENARIO_UPDATE_DUMP] = [
            'tonnage', 'volume', 'selected_rates', 'type_payment',
            'datetime_start', 'valid_datetime', 'id_company', 'status', 'update_at'
        ];
        $scenarios[self::SCENARIO_UPDATE_CRANE] = [
            'tonnage_spec', 'selected_rates', 'type_payment',
            'datetime_start', 'valid_datetime', 'id_company', 'status', 'update_at'
        ];
        $scenarios[self::SCENARIO_UPDATE_EXCAVATOR] = [
            'volume_spec', 'selected_rates', 'type_payment',
            'datetime_start', 'valid_datetime', 'id_company', 'status', 'update_at'
        ];
        $scenarios[self::SCENARIO_FINISH_TRUCK] = [
            'real_datetime_start', 'datetime_finish', 'real_km', 'additional_cost', 'comment_vehicle', 'real_h_loading',
            'real_tonnage', 'real_length', 'real_volume', 'real_remove_awning', 'ClientPaidCash'
        ];
        $scenarios[self::SCENARIO_FINISH_PASS] = [
            'real_datetime_start', 'datetime_finish', 'real_km', 'additional_cost', 'comment_vehicle','real_h_loading',
            'real_passengers', 'ClientPaidCash'
        ];
        $scenarios[self::SCENARIO_FINISH_MANIPULATOR] = [
            'real_datetime_start', 'datetime_finish', 'real_km', 'additional_cost', 'comment_vehicle','real_h_loading',
            'real_tonnage', 'real_length', 'real_tonnage_spec', 'real_length_spec', 'ClientPaidCash'
        ];
        $scenarios[self::SCENARIO_FINISH_DUMP] = [
            'real_datetime_start', 'datetime_finish', 'real_km', 'additional_cost', 'comment_vehicle','real_h_loading',
            'real_tonnage', 'real_volume', 'ClientPaidCash'
        ];
        $scenarios[self::SCENARIO_FINISH_CRANE] = [
            'real_datetime_start', 'datetime_finish', 'real_km', 'additional_cost', 'comment_vehicle','real_h_loading',
            'real_tonnage_spec', 'real_length_spec', 'ClientPaidCash'
        ];
        $scenarios[self::SCENARIO_FINISH_EXCAVATOR] = [
            'real_datetime_start', 'datetime_finish', 'real_km', 'additional_cost', 'comment_vehicle','real_h_loading',
            'real_volume_spec', 'ClientPaidCash'
        ];
        $scenarios[self::SCENARIO_ADD_ID_COMPANY] = ['id_company'];
        $scenarios[self::SCENARIO_UPDATE_PAID_STATUS] = ['paid_status'];
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
            'tonnage' => 'Общий вес груза в тоннах.',
            'length' => 'Необходимая длина кузова в метрах.',
            'width' => 'Необходимая ширина кузова в метрах.',
            'height' => 'Необходимая высота кузова в метрах.',
            'volume' => 'Необходимый объем кузова в м3.',
            'longlength' => 'Груз длинномер.',
            'passengers' => 'Количество пассажиров',
            'ep' => 'Количество "европоддонов" 1,2м х 0,8м.',
            'rp' => 'Количество "русских" поддонов 1м х 1,2м.',
            'lp' => 'Количество нестандартных поддонов 1,2м х 1,2м.',
            'tonnage_spec' => 'Грузоподъемность механизма(стрелы).',
            'length_spec' => 'Длина механизма(стрелы).',
            'volume_spec' => 'Объем механизма(ковша).',
            'cargo' => 'Описание груза.',
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
            'avans_client' => 'Аванс'

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
        if(($this->id_vehicle_type == Vehicle::TYPE_TRUCK || $this->body_typies == Vehicle::BODY_dump || $this->body_typies == Vehicle::BODY_manipulator)
            && !$this->$attribute){
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
                . 'Это займет у Вас 1 имнуту. До этого Вы можете выбрать только наличную форму оплаты. '
            );
        }
    }

    public function getSuitableRates($distance){
        $result = [];
        $priceZones = PriceZone::find()->where(['veh_type' => $this->id_vehicle_type])->andWhere(['status' => PriceZone::STATUS_ACTIVE]);

        switch ($this->id_vehicle_type) {
            case Vehicle::TYPE_TRUCK:
                $priceZones = $priceZones
                    ->andFilterWhere(['>=', 'tonnage_max', $this->tonnage])
                    ->andFilterWhere(['>=', 'volume_max', $this->volume])
                    ->andFilterWhere(['>=', 'length_max', $this->length])
                    ->andFilterWhere(['longlength' => $this->longlength])->orderBy(['r_km'=>SORT_ASC, 'r_h'=>SORT_ASC])->all()
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
                switch ($this->body_typies[0]){
                    case Vehicle::BODY_manipulator:
                        $priceZones = $priceZones
                            ->andFilterWhere(['>=', 'tonnage_spec_max', $this->tonnage_spec])
                            ->andFilterWhere(['>=', 'length_spec_max', $this->length_spec])
                            ->andFilterWhere(['>=', 'tonnage_max', $this->tonnage])
                            ->andFilterWhere(['>=', 'length_max', $this->length])
                        ;
                        break;
                    case Vehicle::BODY_dump:
                        $priceZones = $priceZones
                            ->andFilterWhere(['>=', 'tonnage_max', $this->tonnage])
                            ->andFilterWhere(['>=', 'volume_max', $this->volume])
                        ;
                        break;
                    case Vehicle::BODY_crane:
                        $priceZones = $priceZones
                            ->andFilterWhere(['>=', 'tonnage_spec_max', $this->tonnage_spec])
                            ->andFilterWhere(['>=', 'length_spec_max', $this->length_spec])
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
        foreach ($priceZones as $priceZone) {
            foreach ($this->body_typies as $body_type) {
                if ($priceZone->hasBodyType($body_type))
                    $result[$priceZone->unique_index] = 'Тарифная зона ' . $priceZone->id;
//                continue;
            }
        }
        return $result;
    }

    public function getSuitableRatesCheckboxList($distance = null, $discount = null){
        $suitable_rates = $this->getSuitableRates($distance);
        $return = [];
        foreach ($suitable_rates as $id => $suitable_rate){
            $PriceZone = PriceZone::findOne(['unique_index' => $id]);
            $return[$PriceZone->unique_index] = ' &asymp; ' . $PriceZone->CostCalculationWithDiscountHtml($distance,$discount)
                . ' руб.* '
                . ShowMessageWidget::widget([
                    'helpMessage' => $PriceZone->printHtml(),
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
        if(!$user_id) {
            $discount_cash = $SettingClient->not_registered_discount_cash;
            $discount_card = $SettingClient->not_registered_discount_card;
        } else {
            $user = User::findOne(['id' => $user_id]);
            if ($user->canRole('user')) {
                $discount_cash = $SettingClient->user_discount_cash;
                $discount_card = $SettingClient->user_discount_card;
            }
            if ($user->canRole('client') || $user->canRole('car_owner')) {
                $discount_cash = $SettingClient->client_discount_cash;
                $discount_card = $SettingClient->client_discount_card;
            }
            if ($user->canRole('client') || $user->canRole('vip_car_owner')) {
                $discount_cash = $SettingClient->vip_client_discount_cash;
                $discount_card = $SettingClient->vip_client_discount_card;
            }
        }
        if($type_payment == Payment::TYPE_CASH) return $discount_cash;
        if($type_payment == Payment::TYPE_SBERBANK_CARD) return $discount_card;
        return 0;
    }

    public function getFinishPriceZone(){
        $result = [];
        $priceZones = PriceZone::find()->where(['veh_type' => $this->id_vehicle_type]);

        switch ($this->id_vehicle_type) {
            case Vehicle::TYPE_TRUCK:
                $priceZones = $priceZones
                    ->andFilterWhere(['>=', 'tonnage_max', $this->real_tonnage])
                    ->andFilterWhere(['>=', 'volume_max', $this->real_volume])
                    ->andFilterWhere(['>=', 'length_max', $this->real_length])
                    ->andFilterWhere(['longlength' => $this->real_longlength])->orderBy(['r_km'=>SORT_ASC, 'r_h'=>SORT_ASC])->all()
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
                            ->andFilterWhere(['<=', 'length_spec_max', $this->real_length_spec])
                            ->andFilterWhere(['<=', 'tonnage_max', $this->real_tonnage])
                            ->andFilterWhere(['<=', 'length_max', $this->real_length])
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
                            ->andFilterWhere(['<=', 'tonnage_spec_min', $this->real_tonnage_spec])
                            ->andFilterWhere(['<=', 'length_spec_min', $this->real_length_spec])
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
//        return $result;
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
        if($real_pricezone->r_km < $pricezone_for_vehicle->r_km || $real_pricezone->r_h < $pricezone_for_vehicle->r_h){
            $real_pricezone = $pricezone_for_vehicle;
        }

        return $real_pricezone->unique_index;
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            if(count($this->body_typies)) {
                foreach ($this->body_typies as $body_type_ld) {
                    if($body_type_ld) {
                        $BodyType = BodyType::findOne(['id' => $body_type_ld]);
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
                foreach ($this->selected_rates as $selected_rate_uniqu_index) {
                    $PriceZone = PriceZone::findOne(['unique_index' => $selected_rate_uniqu_index]);
                    $this->link('priceZones', $PriceZone);
                }
            }
            parent::afterSave($insert, $changedAttributes);
            $this->changeStatus(self::STATUS_NEW, $this->id_user);
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
            parent::afterSave($insert, $changedAttributes);
        }
    }

    public function afterFind()
    {
        $this->body_typies = ArrayHelper::getColumn($this->bodyTypies, 'id');
        $this->loading_typies = ArrayHelper::getColumn($this->loadingTypies, 'id');
        $this->suitable_rates = self::getSuitableRates($this->route->distance);
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

    public function getLoadingTypies(){
        return $this->hasMany(LoadingType::className(), ['id' => 'id_loading_type'])
            ->viaTable('XorderXloadingtype', ['id_order' => 'id']);
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

    public function getShortInfoForClient($finish = false){
        $return = 'Тип ТС: ' . $this->vehicleType->type .'.<br> Тип(ы) кузова: ';
        $bTypies =  '';
        if($finish){
            $bTypies .= $this->vehicle->bodyTypeText . ', ';
        } else {
            foreach ($this->body_typies as $bodyType) {
                $bTypies .= BodyType::findOne($bodyType)->body . ', ';
            }
            $bTypies = substr($bTypies, 0, -2);
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
        $return .= $bTypies . '. <br>';
        switch ($this->id_vehicle_type) {
            case Vehicle::TYPE_TRUCK:
                $return .= 'Вес: ' . $tonnage . ' т. ';
                $return .= ($length)?$length. 'м * ':'--.  * ';
                $return .= ($height)?$height. 'м * ':'-- * ';
                $return .= ($width)?$width . 'м ':'-- ';
                $return .= ' (Д*В*Ш). ';
                $return .= 'Объем: ';
                $return .= ($volume)?$volume.' м3 ':'-- ';
                $return .= ($longlength)?' Груз-длинномер.<br>':'<br>';
                $lTypies = 'Погрузка/разгрузка: ';
                foreach ($this->loading_typies as $loadingType) {
                    $lTypies .= LoadingType::findOne($loadingType)->type . ', ';
                }
                $lTypies = substr($lTypies, 0, -2) . '.';
                $return .= ($finish)? '' : $lTypies . '. <br>';
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

    public function getClientInfo($html = true){
        $return = '';
        $return .= $this->profile->fioFull
            . '<br>'
            . 'Телефон: '. functions::getHtmlLinkToPhone($this->user->username, $html);
        if($this->profile->phone2) $return .= ' (доп. тел.: ' . functions::getHtmlLinkToPhone($this->profile->phone2, $html);
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

    public function getClientInfoWithoutPhone(){
        $return = '';
        $return .= $this->profile->fioFull
            . '<br>'
        ;
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

    public function changeStatus($newStatus, $id_client, $id_vehicle = null){
        $url_client = Url::to(['/order/client'], true);
        $url_vehicle = Url::to(['/order/vehicle'], true);
        $email_from = Yii::$app->params['logistEmail'];
        $email_client = User::findOne($id_client)->email;
        if($id_vehicle) {
            $vehicle = Vehicle::findOne($this->id_vehicle);
            $email_vehicle = User::findOne($vehicle->id_user)->email;
        }
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
                        .'ТС: ' .$vehicle->brandAndNumber . '<br>'
                        . $this->getFullNewInfo(false, true, false) . '<br>'
                        .'. <br> Клиент при желании может оценить Ваше действие, что повлияет на Ваш рейтинг водителя.'
                    ;
                    $message_client = 'Водитель (ТC: '. $vehicle->brandAndNumber .') отказался от заказа.<br> Поиск ТС продолжится до '
                        . $valid_datetime
                        . '<br>. Вы можете оценить действия водителя в Личном кабинете, в разделе Уведомления. <br>'
                        . $this->getFullNewInfo(true, false, true, false) . '<br>';
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
                    . $this->getFullNewInfo(true);
                $this->discount = $this->getDiscount($this->id_user);
                break;
            case self::STATUS_VEHICLE_ASSIGNED:
                $vehicle = $this->vehicle;
                $title_client = 'Заказ №'.$this->id.' принят водителем (' . $vehicle->brandAndNumber . ').';
                $title_vehicle = 'Вы приняли заказ №'.$this->id.'.';
                $message_vehicle = 'Вы приняли заказ №'
                    . $this->id  . '<br>'
                    . $this->getFullInfoAboutVehicle(true, true, true, false) . '<br>'
                    . $this->getFullNewInfo(false, true, false, false)
                    . '<br><strong>Телефоны клиента в разделе Заказы на вкладке "В процессе...". </strong><br>';


                $message_client = $this->getFullInfoAboutVehicle(false, false, false)
                    . '<br><strong>Телефоны, паспорт и ВУ водителя в разделе Заказы на вкладке "В процессе...". </strong><br><br>'
                    . $this->getFullNewInfo(true,true, false, false);

                $email_to_vehicle = true;
                $push_to_vehicle = true;
                $this->id_car_owner = $vehicle->user->id;
                $this->discount = $this->getDiscount($id_client);

                $this->deleteEventChangeStatusToExpired();

                break;
            case self::STATUS_EXPIRED:
                $title_client = 'Заказ №' . $this->id . '. Машина не найдена.';
                $message_client = 'Вы можете повторить поиск в разделе "Заказы" на вкладке "Отмененные".';

                $this->FLAG_SEND_EMAIL_STATUS_EXPIRED = 1;
                break;
            case self::STATUS_CONFIRMED_VEHICLE:
                $title_client = 'Заказ №'.$this->id.' выполнен.';
                $title_vehicle = 'Заказ №'.$this->id.'. Вы подтвердили выполнение заказа.';
                $message_vehicle = $this->CalculateAndPrintFinishCost(false, true)['text'];
                $message_client = $this->CalculateAndPrintFinishCost(false, false, true)['text'];
                $email_to_vehicle = true;
                $push_to_vehicle = true;

                $message_can_review_client = true;
                $message_can_review_vehicle = true;

                $client_id_to_review = $vehicle->user->id;
                $client_id_from_review = $id_client;
                $vehicle_id_to_review = $id_client;
                $vehicle_id_from_review = $vehicle->user->id;
                $event_review = Review::EVENT_ORDER_CANCELED;

                if($this->type_payment == Payment::TYPE_CASH)$this->paid_status = self::PAID_YES;
                else $this->paid_status = self::PAID_NO;
                $this->cost_finish = $this->getFinishCost(false);
                $this->cost_finish_vehicle = $this->finishCostForVehicle;
                break;
            case self::STATUS_CONFIRMED_CLIENT:

                break;
            case self::STATUS_CANCELED:
                if($this->status == self::STATUS_NEW || $this->status == self::STATUS_IN_PROCCESSING){
                    $title_client = 'Заказ №'.$this->id.' отменен.';
                    $message_client = 'Вы отменили Ваш заказ.  <br>'
                        . $this->getFullNewInfo(true, false, true, false);
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
                    $message_client = 'Вы отменили Заказ. Пожалуйста, позвоните водителю и сообщите об отмене заказа <br>'
                        . $this->vehicleFioAndPhone . '<br>'
                        . $this->getFullNewInfo(true, false, true, false) . '<br>';
                    $push_to_vehicle = true;
                    $email_to_vehicle = true;

                    $message_can_review_vehicle = true;
                    $vehicle_id_to_review = $id_client;
                    $vehicle_id_from_review = $vehicle->user->id;
                    $event_review = Review::EVENT_ORDER_CANCELED;

                    $this->id_vehicle = null;
                    $this->id_car_owner = null;
                    $this->id_driver = null;
                    $this->id_pricezone_for_vehicle = null;

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
        //Емэил Водителю
        if($email_to_vehicle) {
            functions::sendEmail(
                $email_vehicle,
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
        if($id_client) {
            $Message_to_client = new Message([
                'id_to_user' => $id_client,
                'title' => $title_client,
                'text' => $message_client,
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

        if($message_can_review_client){
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
        $return .= $this->getShortInfoForClient() . ' <br>';
        $return .= ($showPriceForVehicle) ? 'Тарифная зона №' . $this->id_pricezone_for_vehicle . '. <br>' : '';
        $return .= ($showPriceZones) ? 'Тарифные зоны: ' . $this->idsPriceZones . '. <br>' :'';
        $return .= 'Тип оплаты: ' . $this->paymentText . '. <br>';
        $return .= ($showClientPhone)
            ?'Заказчик:' . $this->getClientInfo($html) . ' <br>'
            :'Заказчик:' . $this->clientInfoWithoutPhone . ' <br>';

        return $return;
    }

    public function getFullFinishInfo($showClientPhone = false, $realRoute = null){
        if($this->realRoute) $real_route = $this->realRoute;
        else {
            if(!$realRoute) return false;
            $real_route = $realRoute;
        }
        $return = 'Заказ №' . $this->id;
        $return .= '<br>Время выезда: ' .  $this->real_datetime_start .'<br>';
        $return .= 'Время возвращения: ' .  $this->datetime_finish .'<br>';
        $return .= 'Маршрут: ' . $real_route->fullRoute . '<br>';
        $return .= $this->getShortInfoForClient(true) . ' <br>';
        $return .= 'Тарифная зона №' . $this->id_price_zone_real . '. <br>';
        $return .= 'Тип оплаты: ' . $this->paymentText . '. <br>';
        $return .= ($showClientPhone)
            ?'Заказчик:' . $this->clientInfo . ' <br>'
            :'Заказчик:' . $this->clientInfoWithoutPhone . ' <br>';

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
                        $res = ['real_tonnage','real_length', 'real_tonnage_spec', 'length_spec'];
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

    public function copyValueToRealValue(){
        if(!$this) return false;
        $body_type = $this->vehicle->bodyType->id;
        switch ($this->id_vehicle_type){
            case Vehicle::TYPE_TRUCK:
                $this->real_tonnage = $this->tonnage;
                $this->real_length = $this->length;
                $this->real_volume = $this->volume;
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

                    if($min_cost > $real_cost){
                        $cost = $min_cost;
                        $text .= '<br>Минимальная оплата при пробеге более 120км.: ' . $real_pz->min_price;
                    } else {
                        $cost = $real_cost;
                    }
                    $text .= '<br>Итого за пробег: ' . $cost . 'р. ';
                    $text .= '<br>Время потраченное на погрузку/разгрузку/ожидание: ' . $this->real_h_loading . 'ч. ';
                    $text .= '<br>Бесплатное время на погрузку/разгрузку/ожидание: ' . $real_pz->h_loading . 'ч. ';
                    if($this->real_h_loading && ($this->real_h_loading - $real_pz->h_loading)>0){
                        $cost += ($this->real_h_loading - $real_pz->h_loading) * $real_pz->r_loading;
                        $text .= '<br>Стоимость часа (сверх бесплатных) на погрузку/разгрузку/ожидание: ' . $real_pz->r_loading . 'р. ';
                        $text .= '<br>Итого за лишнее время на погрузку/разгрузку/ожидание: ';
                        $text .=  $real_pz->r_loading * ($this->real_h_loading - $real_pz->h_loading);
                        $text .= 'р. ';
                    }
//                    $text .= '<br>Время потраченное на погрузку/разгрузку/ожидание: ' . $this->real_h_loading . 'ч. ';
//                    $text .= '<br>Бесплатное время на погрузку/разгрузку/ожидание: ' . $real_pz->h_loading . 'ч. ';
//                    $text .= '<br>Стоимость часа (сверх бесплатных) на погрузку/разгрузку/ожидание: ' . $real_pz->r_loading . 'р. ';
//                    $text .= '<br>Итого за лишнее время на погрузку/разгрузку/ожидание: ';
//                    $text .=  (($this->real_h_loading - $real_pz->h_loading)>0) ? $real_pz->h_loading * ($this->real_h_loading - $real_pz->h_loading) : '0';
//                    $text .= 'р. ';

                }
            } else {
                $real_cost = $this->real_h * $real_pz->r_h;
                ($min_cost > $real_cost)? $cost = $min_cost : $cost = $real_cost;
                $text .= '<br>Время работы (с учетом дороги от/до г.Обнинск): ' . $this->real_h . 'ч. ';
                $text .= '<br>Стоимость 1 часа: ' . $real_pz->r_h . 'р. ';
            }
            if($this->real_remove_awning && $real_pz->remove_awning) {
                $text .= '<br>Количество "растентовок" сверху или сбоку: ' . $this->real_remove_awning . '. ';
                $text .= '<br>Стоимость одной "растентовки" одной стороны: ' . $real_pz->remove_awning . 'р. ';
                $cost += $this->real_remove_awning * $real_pz->remove_awning;
                $text .= '<br>Итого за "растентовку": ' . $this->real_remove_awning * $real_pz->remove_awning;
            }

            if($this->additional_cost){
                $cost += $this->additional_cost;
                $text .= '<br>Дополнительные расходы: ' . $this->additional_cost . 'Р. ';
            }
            if($withDiscount){
                $cost = $this->getFinishCost(false);
            }
            $text .= '<br>Комментарии водителя: ' . $this->comment_vehicle;
            $return['cost'] =  $cost;
//            if($withDiscount){
//                $cost = $this->getFinishCost($html);
//            }
            $text .= '<br>Тип оплаты: ' . $this->getPaymentText(false);
            if($this->discount && !$forVehicle) {
                $text .= '<br>Скидка: ' . $this->discount . ($html) ? Html::img('/img/icons/discount-16.png', ['title' => 'Действует скидка!']) : '%';
            }
            $text .= '<br><br><strong>Итого к оплате ' . $cost . ' руб.</strong>';
            $return['text'] =  $text;
            return $return;
        }
        return ['text' => 'error', 'cost' => 0];
    }

    public function getReal_h(){
        if(!$this->real_datetime_start || ! $this->datetime_finish) return false;
        return ceil((strtotime($this->datetime_finish) - strtotime($this->real_datetime_start))/3600/0.5)*0.5;
    }

    public function getFinishCost($html = true){

        if($html){
            return ($this->discount)
                ? '<s>' . $this->cost . '</s> '
                . '<strong> '
                . round($this->cost - (($this->cost - $this->additional_cost) * $this->discount/100))
                . '</strong>'
                : $this->cost;
        } else{
            return ($this->discount)
                ? round($this->cost - (($this->cost - $this->additional_cost) * $this->discount/100))
                : $this->cost;
        }
    }

    public function getFinishCostForVehicle(){
        return round($this->CalculateAndPrintFinishCost(false, true, false)['cost']);
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
}


