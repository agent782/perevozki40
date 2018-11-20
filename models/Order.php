<?php

namespace app\models;

use app\components\functions\functions;
use FontLib\Table\Type\post;
use Yii;
use app\components\DateBehaviors;
use yii\helpers\Url;

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
 * @property float $cost
 * @property integer $id_payment
 * @property integer $type_payment
 * @property integer $status
 * @property integer $paid_status
 * @property string $comment
 * @property string $statusText
 * @property string $paidText
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
//    const PAID_CANCELED = 2;

    public $body_typies;
    public $loading_typies;
    public $suitable_rates;
    public $selected_rates;

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
            [[ 'datetime_access','datetime_finish'],
//                'date' ,'format' => 'php:d.M.Y H:ii'
                'safe'
            ],
            [['id',   'suitable_rates', 'datetime_access', 'id_route', 'id_route_real', 'id_price_zone_real', 'cost', 'comment'], 'safe'],
            [['id','longlength', 'ep', 'rp', 'lp', 'id_route', 'id_route_real',
                'id_payment', 'status', 'type_payment', 'passengers'], 'integer'],
            [['tonnage', 'length', 'width', 'height', 'volume', 'tonnage_spec', 'length_spec', 'volume_spec'], 'number'],
            [['cargo'], 'string'],
            [['create_at', 'update_at'], 'default', 'value' => date('d.m.Y h:i')],
            ['status', 'default', 'value' => self::STATUS_NEW],
            ['paid_status', 'default', 'value' => self::PAID_NO],
        ];
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
            'valid_datetime' => 'Крайний срок подачи ТС по заказу',
            'id_route' => 'Id маршруты',
            'id_route_real' => 'Id реального маршрута',
            'type_payment' => 'Способ оплаты:',
            'status' => 'Статус',
            'statusText' => 'Статус',
            'paidText' => 'Оплата'
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
                    $LoadingType = LoadingType::findOne(['id' => $loading_type_id]);
                    $this->link('loadingTypies', $LoadingType);
                }
            }
            if($this->selected_rates) {
                foreach ($this->selected_rates as $selected_rate_id) {
                    $PriceZone = PriceZone::findOne(['id' => $selected_rate_id]);
                    $this->link('priceZones', $PriceZone);
                }
            }
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
        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }

    public function setStatus($status){
        $this->status = $status;
        return $this->update();
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

    public function getStatusText(){
        $res = 'Новый';
        switch ($this->status){
            case self::STATUS_NOT_ACCEPTED:
                $res = 'Не принят';
                break;
            case self::STATUS_EXPIRED:
                $res = 'Просрочен';
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
       return ($this->paid_status) ? 'Оплачен' : 'Не оплачен';
    }

}


