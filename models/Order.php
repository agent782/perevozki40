<?php

namespace app\models;

use app\components\functions\functions;
use FontLib\Table\Type\post;
use Yii;
use app\components\DateBehaviors;
use yii\bootstrap\Html;
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
 * @property integer $FLAG_SEND_EMAIL_STATUS_EXPIRED
 * @property User $user;
 * @property object $vehicle
 * @property Route $route
 * @property string $paymentText
 * @property string $priceZonesWithInfo
 * @property integer $id_pricezone_for_vehicle
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

    const SCENARIO_UPDATE_STATUS = 'update_status';
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
            [[ 'id_vehicle','datetime_access','datetime_finish', 'FLAG_SEND_EMAIL_STATUS_EXPIRED',
                'id_pricezone_for_vehicle'],
                'safe'
            ],
            [['id',   'suitable_rates', 'datetime_access', 'id_route', 'id_route_real', 'id_price_zone_real', 'cost', 'comment'], 'safe'],
            [['id','longlength', 'ep', 'rp', 'lp', 'id_route', 'id_route_real',
                'id_payment', 'status', 'type_payment', 'passengers'], 'integer'],
            [['tonnage', 'length', 'width', 'height', 'volume', 'tonnage_spec', 'length_spec',
                'volume_spec'], 'number'],
            [['cargo'], 'string'],
            [['create_at', 'update_at'], 'default', 'value' => date('d.m.Y h:i')],
            ['status', 'default', 'value' => self::STATUS_NEW],
            ['paid_status', 'default', 'value' => self::PAID_NO],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios(); // TODO: Change the autogenerated stub
        $scenarios[self::SCENARIO_UPDATE_STATUS] = ['status'];
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
            'priceZonesWithInfo' => 'Тарифы'
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
// Создание myaql события на изменение статуса заказа на просрочен при достижении времени valid_datetime
            Yii::$app->db->createCommand('
                CREATE EVENT IF NOT EXISTS cancel_order_'
                . $this->id .
                ' ON SCHEDULE AT (FROM_UNIXTIME ('.
                $this->valid_datetime
                . '))
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

            )->query();

            functions::sendEmail(
                [
                    Yii::$app->user->identity->email,
                    Yii::$app->params['logistEmail']['email']
                ],
                Yii::$app->params['logistEmail'],
                'Заказ №'.$this->id.' офоррмлен.',
                [
                    'modelOrder' => $this
                ],
                [
                    'html' => 'views/Order/newOrder_html',
                    'text' => 'views/Order/newOrder_text'
                ]
            );
            $Message = new Message([
                'id_to_user' => Yii::$app->user->id,
                'title' => 'Заказ №'.$this->id.' оформлен.',
                'text' => 'Заказ оформлен.',
                'url' => Url::to(['/order/view', 'id' => $this->id], true),
                'push_status' => Message::STATUS_NEED_TO_SEND,
                'email_status' => Message::STATUS_NEED_TO_SEND,
            ]);
            $Message->save();
            $Message->sendPush();

            functions::setFlashSuccess('Заказ добавлен в список заказов.');
            parent::afterSave($insert, $changedAttributes);

            return true;
        } else {
            functions::setFlashWarning('Ошибка на сервере. Попробуйте позже.');
            parent::afterSave($insert, $changedAttributes);

            return false;
        }
    }

    public function beforeDelete()
    {
        $this->unlinkAll('bodyTypies', true);
        $this->unlinkAll('loadingTypies', true);
        $this->unlinkAll('priceZones', true);
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
            ->viaTable('XorderXrate', ['id_order' => 'id']);
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
        $return = $this->vehicleType->type;

        $bTypies = ' (';
        if($this->status == self::STATUS_CONFIRMED_VEHICLE
            || $this->status == self::STATUS_CONFIRMED_CLIENT
        ){

        }
        else {
            foreach ($this->bodyTypies as $bodyType) {
                $bTypies .= $bodyType->body . ', ';
            }
            $bTypies = substr($bTypies, 0, -2);
            $return .= $bTypies . '). <br>';

            switch ($this->id_vehicle_type) {
                case Vehicle::TYPE_TRUCK:
                    $return .= $this->tonnage . ' тонн(ы). ';
                    $return .= ($this->length)?$this->length. ' * ':'--.  * ';
                    $return .= ($this->height)?$this->height. ' * ':'-- * ';
                    $return .= ($this->width)?$this->width:'--';
                    $return .= ' (Д*В*Ш). ';
                    $return .= 'Объем: ';
                    $return .= ($this->volume)?$this->volume.' м3':'--';
                    $return .= ($this->longlength)?' Груз-длинномер.':'';
                    $lTypies = 'Погрузка/разгрузка: ';
                    $return .= "<br>";
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
        }
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
        $return = '(' . $route->distance . 'km) '. $route->startCity . ' -';
        for($i = 1; $i<9; $i++){
            $attribute = 'route' . $i;
            if($route->$attribute) $return .= '... -';
        }
        $return .=  ' '.$route->finishCity ;
        return $return;
    }

    public function getClientInfo(){
        $return = '';
        $return .= $this->profile->fioFull
            . '<br>'
            . '<a href="tel:+7'. $this->user->username .'">'. $this->user->username. '</a>'
        ;

        return $return;
    }

    public function getPaymentText(){
        switch ($this->type_payment){
            case Payment::TYPE_CASH:
                return 'Наличными водителю';
                break;
            case Payment::TYPE_SBERBANK_CARD:
                return 'На карту Сбербанка';
                break;
            case Payment::TYPE_BANK_TRANSFER:
                return 'По безналичному расчету';
                break;
        }
    }

    public function getPriceZonesWithInfo(){
        $return = '';
        foreach ($this->priceZones as $priceZone){
            $return .= $priceZone->getTextWithShowMessageButton($this->route->distance);
        }
        return $return;
    }

}


