<?php

namespace app\models;

use app\components\functions\functions;
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
 * @property integer $datetime_start
 * @property integer $datetime_finish
 * @property integer $datetime_access
 * @property integer $valid_datetime
 * @property integer $create_at
 * @property integer $update_at
 * @property integer $id_route
 * @property integer $id_route_real
 * @property integer $id_price_zone_real
 * @property integer $id_user
 * @property integer $id_company
 * @property integer $id_vehicle
 * @property integer $id_driver
 * @property float $cost
 * @property integer $id_payment
 * @property integer $type_payment
 * @property integer $status
 * @property integer $paid_status
 * @property string $comment
 * @property string $statusText
 * @property string $paidText
 * @property string $shortRoute
 * @property string $clientInfo
 * @property string $clientInfoWithoutPhone
 * @property integer $FLAG_SEND_EMAIL_STATUS_EXPIRED
 * @property User $user;
 * @property Vehicle $vehicle
 * @property Route $route
 * @property string $paymentText
 * @property string $priceZonesWithInfo
 * @property integer $id_pricezone_for_vehicle
 * @property Driver $driver
 * @property string $fullNewInfo
 * @property Company $company
 * @property string $idsPriceZonesWithPriceAndShortInfo
 * @property string $idsPriceZones
 *
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

    const PAID_NO = 0;
    const PAID_YES = 1;
    const PAID_PARTIALLY = 2;
//    const PAID_CANCELED = 2;

    public $body_typies;
    public $loading_typies;
    public $suitable_rates;
    public $selected_rates;

    const SCENARIO_UPDATE_TRUCK = 'update_truck';
    const SCENARIO_UPDATE_PASS = 'update_pass';
    const SCENARIO_UPDATE_MANIPULATOR = 'update_manipulator';
    const SCENARIO_UPDATE_DUMP = 'update_dump';
    const SCENARIO_UPDATE_CRANE = 'update_crane';
    const SCENARIO_UPDATE_EXCAVATOR = 'update_excavator';
    const SCENARIO_UPDATE_STATUS = 'update_status';
    const SCENARIO_ACCESSING = 'accessing';
    const SCENARIO_NEW_ORDER = 'new_order';
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
            [['datetime_start', 'valid_datetime', 'type_payment'], 'required'],
            ['passengers', 'validatePassengers', 'skipOnEmpty' => false],
            [['id_company'], 'validateConfirmCompany', 'skipOnEmpty' => false],
            [['datetime_access','datetime_finish', 'FLAG_SEND_EMAIL_STATUS_EXPIRED',
                'id_pricezone_for_vehicle'],
                'safe'
            ],
            [['id',   'suitable_rates', 'datetime_access', 'id_route', 'id_route_real', 'id_price_zone_real', 'cost', 'comment'], 'safe'],
            [['id','longlength', 'ep', 'rp', 'lp', 'id_route', 'id_route_real',
                'id_payment', 'status', 'type_payment', 'passengers'], 'integer'],
            [['tonnage', 'length', 'width', 'height', 'volume', 'tonnage_spec', 'length_spec',
                'volume_spec'], 'number'],
            [['cargo'], 'string'],
            [['create_at', 'update_at'], 'default', 'value' => date('d.m.Y H:i')],
            ['status', 'default', 'value' => self::STATUS_NEW],
            ['paid_status', 'default', 'value' => self::PAID_NO],
            [['id_vehicle', 'id_driver'], 'required', 'message' => 'Выберите один из вариантов']
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios(); // TODO: Change the autogenerated stub
        $scenarios[self::SCENARIO_UPDATE_STATUS] = ['status'];
        $scenarios[self::SCENARIO_ACCESSING] = ['id_vehicle', 'id_driver'];
        $scenarios[self::SCENARIO_NEW_ORDER] = ['id_vehicle_type','body_typies', 'loading_typies',
            'tonnage', 'selected_rates', 'type_payment', 'datetime_start', 'valid_datetime',
            'passengers','id_company', 'status', 'create_at', 'update_at'];
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
            'datetime_finish' => 'Дата и время завершения заказа.',
            'datetime_access' => 'Дата и  время подтверждения Заказчиком.',
            'valid_datetime' => 'Выполнять поиск ТС до:',
            'id_route' => 'Id маршруты',
            'id_route_real' => 'Id реального маршрута',
            'type_payment' => 'Способ оплаты:',
            'status' => 'Статус',
            'statusText' => 'Статус',
            'paidText' => 'Оплата',
            'create_at' => 'Дата оформления заказа',
            'clientInfo' => 'Заказчик',
            'shortInfoForClient' => 'ТС',
            'paymentText' => 'Тип оплаты',
            'priceZonesWithInfo' => 'Тарифы',
            'id_vehicle' => 'ТС',
            'id_driver' => 'Водитель'
        ];
    }

    public function behaviors()
    {
        return [
            'convertDateTime' => [
                'class' => 'app\components\DateBehaviors',
                'dateAttributes' => ['datetime_start', 'datetime_finish','datetime_access' ,'valid_datetime', 'create_at', 'update_at'],
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

    public function getSuitableRates($distance){
        $result = [];
        $priceZones = PriceZone::find()->where(['veh_type' => $this->id_vehicle_type]);

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
                            ->andFilterWhere(['<=', 'length_spec_max', $this->length_spec])
                            ->andFilterWhere(['<=', 'tonnage_max', $this->tonnage])
                            ->andFilterWhere(['<=', 'length_max', $this->length])
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
                            ->andFilterWhere(['<=', 'tonnage_spec_min', $this->tonnage_spec])
                            ->andFilterWhere(['<=', 'length_spec_min', $this->length_spec])
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
                    $result[$priceZone->id] = 'Тарифная зона ' . $priceZone->id;
//                continue;
            }
        }
        return $result;
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
                foreach ($this->selected_rates as $selected_rate_id) {
                    $PriceZone = PriceZone::findOne(['id' => $selected_rate_id]);
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
                    $PriceZone = PriceZone::findOne(['id' => $selected_rate_id]);
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
        $this->selected_rates = ArrayHelper::getColumn($this->priceZones, 'id');
        parent::afterFind(); // TODO: Change the autogenerated stub
    }

    public function beforeDelete()
    {
        $this->unlinkAll('bodyTypies', true);
        $this->unlinkAll('loadingTypies', true);
        $this->unlinkAll('priceZones', true);
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

    public function getBodyTypies(){
        return $this->hasMany(BodyType::className(), ['id' => 'id_bodytype'])
            ->viaTable('XorderXtypebody', ['id_order' => 'id']);
    }
    public function getLoadingTypies(){
        return $this->hasMany(LoadingType::className(), ['id' => 'id_loading_type'])
            ->viaTable('XorderXloadingtype', ['id_order' => 'id']);
    }
    public function getPriceZones(){
        return $this->hasMany(PriceZone::className(), ['id' => 'id_rate'])
            ->viaTable('XorderXrate', ['id_order' => 'id'])->orderBy('r_km', 'r_h');
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
           case self::PAID_NO:
               return 'Оплачен';
               break;
           case self::PAID_NO:
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

    public function getShortInfoForClient(){
        $return = 'Тип ТС: ' . $this->vehicleType->type .'.<br> Тип(ы) кузова: ';

        $bTypies = ' (';
        foreach ($this->bodyTypies as $bodyType) {
            $bTypies .= $bodyType->body . ', ';
        }
        $bTypies = substr($bTypies, 0, -2);
        $return .= $bTypies . '). <br>';

        switch ($this->id_vehicle_type) {
            case Vehicle::TYPE_TRUCK:
                $return .= 'Вес: ' . $this->tonnage . ' тонн(ы). ';
                $return .= ($this->length)?$this->length. 'м * ':'--.  * ';
                $return .= ($this->height)?$this->height. 'м * ':'-- * ';
                $return .= ($this->width)?$this->width . 'м ':'-- ';
                $return .= ' (Д*В*Ш). ';
                $return .= 'Объем: ';
                $return .= ($this->volume)?$this->volume.' м3 ':'-- ';
                $return .= ($this->longlength)?' Груз-длинномер.<br>':'<br>';
                $lTypies = 'Погрузка/разгрузка: ';
                foreach ($this->loadingTypies as $loadingType) {
                    $lTypies .= $loadingType->type . ', ';
                }
                $lTypies = substr($lTypies, 0, -2) . '.';
                $return .= $lTypies;
                break;
            case Vehicle::TYPE_PASSENGER:
                $return .= $this->passengers . ' пассажира(ов)';
                break;

            case Vehicle::TYPE_SPEC:
                switch ($this->bodyTypies[0]->id) {
                    case Vehicle::BODY_manipulator:
                        $return .= $this->tonnage . ' тонн(ы). ';
                        $return .= 'Стрела: ';
                        $return .= ($this->tonnage_spec)?$this->tonnage_spec . ' т., ': '-- т., ';
                        $return .= ($this->length_spec)?$this->length_spec . ' м.': '-- м.';
                        break;
                    case Vehicle::BODY_dump:
                        $return .= $this->tonnage . ' тонн(ы). ';
                        $return .= $this->volume . ' м3. ';
                        break;
                    case Vehicle::BODY_crane:
                        $return .= 'Стрела: ';
                        $return .= ($this->tonnage_spec)?$this->tonnage_spec . ' т., ': '-- т., ';
                        $return .= ($this->length_spec)?$this->length_spec . ' м.': '-- м.';
                        break;
                    case Vehicle::BODY_excavator:
                        $return .= 'Ковш: ';
                        $return .= ($this->volume_spec)?$this->volume_spec . ' м3. ': '-- м3.';
                        break;
                    case Vehicle::BODY_excavator_loader:
                        $return .= 'Ковш: ';
                        $return .= ($this->volume_spec)?$this->volume_spec . ' м3. ': '-- м3.';
                        break;
                }
                break;
        }
        $return .= ($this->cargo)?'<br>Комментарии: ' . $this->cargo : '';


        return $return;
    }

    static public function getCountNewOrders(){
        return Order::find()
            ->where(['status' => self::STATUS_NEW])
            ->orWhere(['status' => self::STATUS_IN_PROCCESSING])
            ->count();
    }

    public function getShortRoute(){
        $route = $this->route;
        $return = '(&asymp;' . $route->distance . 'km)* <br>'. $route->startCity . ' <br>';
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

    public function getClientInfo(){
        $return = '';
        $return .= $this->profile->fioFull
            . '<br>'
            . 'Телефон: <a href="tel:+7'. $this->user->username .'">'. $this->user->username. '</a><br>'
        ;
        if($this->id_company) $return .= $this->company->name . '<br>';

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


    public function getPaymentText(){
        return TypePayment::findOne($this->type_payment)->type;
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
                        . $this->getFullNewInfo(false, true) . '<br>'
                        .'. <br> Клиент при желании может оценить Ваше действие, что повлияет на Ваш рейтинг водителя.'
                    ;
                    $message_client = 'Водитель (ТC: '. $vehicle->brandAndNumber .') отказался от заказа.<br> Поиск ТС продолжится до '
                        . $valid_datetime
                        . '<br>. Вы можете оценить действия водителя в Личном кабинете, в разделе Уведомления. <br>'
                        . $this->getFullNewInfo(true) . '<br>';
                    $push_to_vehicle = true;
                    $email_to_vehicle = true;

                   $message_can_review_client = true;
                   $client_id_to_review = $vehicle->id_user;
                   $client_id_from_review = $id_client;

                    $this->valid_datetime = $valid_datetime;
                    $this->id_vehicle = null;
                    $this->id_driver = null;
                    $this->id_pricezone_for_vehicle = null;

                    $this->setEventChangeStatusToExpired();
                    break;
                }

                break;
            case self::STATUS_NEW:
                $title_client = 'Заказ №'.$this->id.' оформлен.';
                $message_client = 'Спасибо за Ваш заказ.  <br>'
                    . $this->getFullNewInfo(true);
                break;
            case self::STATUS_VEHICLE_ASSIGNED:
                $vehicle = Vehicle::findOne($this->id_vehicle);
                $title_client = 'Заказ №'.$this->id.' принят водителем (' . $vehicle->brandAndNumber . ').';
                $title_vehicle = 'Вы приняли заказ №'.$this->id.'.';
                $message_vehicle = 'Вы приняли заказ №'
                    . $this->id
                    . ' на ТС '
                    .  $vehicle->brandAndNumber.' <br>'
                    . 'Водитель: ' . $this->driver->fio
                    . ' (' . $this->driver->passport->fullInfo . '). <br>'
                    . $this->getFullNewInfo(false, true);

                $message_client = 'ТС: ' . $vehicle->brandAndNumber . ' <br>'
                    . 'Водитель: ' . $this->driver->fio
                    . ' (' . $this->driver->passport->fullInfo . '). <br>'
//                    . 'Тарифная зона № ' . $this->id_pricezone_for_vehicle . '. <br>'
                    . $this->getFullNewInfo(true, true);

                $email_to_vehicle = true;
                $push_to_vehicle = true;

                $this->deleteEventChangeStatusToExpired();

                break;
            case self::STATUS_EXPIRED:
                break;
            case self::STATUS_CONFIRMED_VEHICLE:
                $title = 'Заказ №' . $this->id . 'подтвержден водителем.';
                $email_addresses [] = User::findOne($id_client)->email;
                $email_addresses [] = User::findOne($id_vehicle)->email;
                functions::setFlashSuccess('Водитель подтвердил завершение заказа.');
                break;
            case self::STATUS_CONFIRMED_CLIENT:
                $title = 'Заказ №' . $this->id . ' подтвержден клиентом.';
                $email_addresses [] = User::findOne($id_client)->email;
                $email_addresses [] = User::findOne($id_vehicle)->email;
                functions::setFlashSuccess('Клиент подтвердил завершение заказа.');
                $push_to_vehicle = true;
                break;
            case self::STATUS_CANCELED:
                if($this->status == self::STATUS_NEW || $this->status == self::STATUS_IN_PROCCESSING){
                    $title_client = 'Заказ №'.$this->id.' отменен.';
                    $message_client = 'Вы отменили Ваш заказ.  <br>'
                        . $this->getFullNewInfo(true);;
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

    public function getFullNewInfo($showClientPhone = false, $showPriceForVehicle = false, $showPriceZones = true){
        $return = 'Заказ №' . $this->id . ' на ' .  $this->datetime_start .'<br>';
        $return .= 'Маршрут: ' . $this->route->fullRoute . '<br>';
        $return .= $this->getShortInfoForClient() . ' <br>';
        if($showPriceZones) {
            $return .= ($showPriceForVehicle)
                ? 'Тарифная зона №' . $this->id_pricezone_for_vehicle . '. <br>'
                : 'Тарифные зоны: ' . $this->idsPriceZones . '. <br>';
        }
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
}


