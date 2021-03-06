<?php

namespace app\models;

use app\components\functions\functions;
use app\components\SerializeBehaviors;
use FontLib\Table\Type\post;
use Yii;
use app\components\DateBehaviors;
use yii\bootstrap\Html;
use app\components\widgets\ShowMessageWidget;
use yii\helpers\Url;
use app\models\setting\Setting;
use yii\data\ArrayDataProvider;


/**
 * This is the model class for table "vehicles".
 *
 * @property integer $id
 * @property integer $id_user
 * @property float $tonnage
 * @property float $length
 * @property float $width
 * @property float $height
 * @property float $volume
 * @property integer $passengers
 * @property integer $ep
 * @property integer $rp
 * @property integer $lp
 * @property boolean $longlength
 * @property float $tonnage_spec
 * @property float $length_spec
 * @property float $volume_spec
 * @property integer $create_at
 * @property integer $update_at
 * @property integer $status
 * @property string $statusText
 * @property integer $rating
 * @property integer $reg_license_id
 * @property integer $id_vehicle_type
 * @property integer $body_type BodyTypeText
 * @property string $bodyTypeText
 * @property float $description
 * @property string $photo
 * @property LoadingType[] $loadingtypes
 * @property array $loadingtypesText
 * @property BodyType[] $bodyType
 * @property string $brand
 * @property string $longlengthIcon
 * @property string $photoHtml
 * @property string $priceZonesList
 * @property string $error_mes
 * @property User $user
 * @property Profile $profile
 * @property RegLicense $regLicense
 * @property string $brandAndNumber
 * @property string $fullInfo
 * @property PriceZone[] $priceZonesSelect
 * @property array CalendarVehicle $calendarVehicle



 */
class Vehicle extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_ONCHECKING = 2;
    const STATUS_FULL_DELETED = 3;
    const STATUS_NOT_ACTIVE = 4;

    const BODY_ANY = 0;
    const BODY_truck_minivan = 1;
    const BODY_commercial_minibus = 2;
    const BODY_cargo_and_passenger_minibus = 3;
    const BODY_awning_rear_side = 4;
    const BODY_rear_gate_awning = 5;
    const BODY_van = 6;
    const BODY_heated_van = 7;
    const BODY_manipulator = 8;
    const BODY_sedan = 9;
    const BODY_minivan = 10;
    const BODY_hatchback = 11;
    const BODY_crane = 12;
    const BODY_excavator = 13;
    const BODY_dump = 14;
    const BODY_excavator_loader = 15;
    const BODY_pass_minibus = 16;
    const BODY_bus = 17;
    const BODY_side_open = 18;
    const BODY_ref = 19;

    const LOADING_TYPE_BEHIND = 2;
    const LOADING_TYPE_OVERHAND = 3;
    const LOADING_TYPE_SIDEWAYS = 4;

    const TYPE_TRUCK = 1;
    const TYPE_PASSENGER = 2;
    const TYPE_SPEC = 3;

    const SORT_DATE_CREATE = [
        'attributes' => [
            'create_at'
        ],
        'defaultOrder' => [
            'create_at' => SORT_DESC,
        ]
    ];

    const SORT_TRUCK = [
        'enableMultiSort' => true,
        'attributes' => [
            'tonnage',
            'body_type',
            'length',
            'volume',
        ],
        'defaultOrder' => [
            'tonnage' => SORT_ASC,
            'body_type' => SORT_ASC,
            'length' => SORT_ASC,
            'volume' => SORT_ASC,
        ]
    ];
    const SORT_PASS = [
        'enableMultiSort' => true,
        'attributes' => [
            'passengers',
        ],
        'defaultOrder' => [
            'passengers' => SORT_ASC,
        ]
    ];
    const SORT_SPEC = [
        'enableMultiSort' => true,
        'attributes' => [
            'bodyTypeText'
        ],
//        'defaultOrder' => [
//            'vehicles.body_type.body' => SORT_ASC,
//        ]
    ];

    const SCENARIO_UPDATE_TRUCK = 'updateTruck';
    const SCENARIO_UPDATE_PASS = 'updatePass';
    const SCENARIO_UPDATE_SPEC_BODY_manipulator = 'updateSpecManipulator';
    const SCENARIO_UPDATE_SPEC_BODY_dump = 'updateSpecDump';
    const SCENARIO_UPDATE_SPEC_BODY_crane = 'updateSpecCrane';
    const SCENARIO_UPDATE_SPEC_BODY_excavator = 'updateSpecExcavator';

    const SCENARIO_DELETE = 'delete';
    const SCENARIO_CHECK = 'check';

    const SCENARIO_CREATE = 'create';

    public $loadingTypeIds = [];
    public $Price_zones = [];
    public $photoFile;
    public $calendar;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vehicles';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['body_type', 'Price_zones'], 'required',
                'except' => self::SCENARIO_DELETE,
                'message' => 'Выберите хотя бы один из вариантов.'],
            [['tonnage', 'length', 'width', 'height', 'volume','loadingTypeIds',
                'passengers', 'tonnage_spec', 'length_spec', 'volume_spec'],
                'required',
                'on' => [self::SCENARIO_UPDATE_TRUCK, self::SCENARIO_UPDATE_PASS,
                self::SCENARIO_UPDATE_SPEC_BODY_manipulator, self::SCENARIO_UPDATE_SPEC_BODY_crane,
                self::SCENARIO_UPDATE_SPEC_BODY_dump, self::SCENARIO_UPDATE_SPEC_BODY_excavator],
            ],
            [['id_user', 'passengers', 'longlength', 'ep', 'rp', 'lp', 'reg_license_id', 'id_vehicle_type'], 'integer'],
//            [['tonnage', 'length', 'width', 'height', 'volume', 'tonnage_spec', 'length_spec', 'volume_spec'], 'number'],
            [['create_at', 'update_at'], 'default', 'value' => date('d.m.Y h:i')],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['rating', 'default', 'value' => 0],
            [['bodyTypies', 'error_mes'], 'safe'],
            [['tonnage', 'tonnage_spec'], 'number', 'min' => 0.1, 'max' => 50,
                'message' => 'Ввведите число. Для ввода дробного числа используйте символ "."(точка), например : 1.5 , 3.75 и т.п.'],
            [['length'], 'number', 'min' => 0.5,'max' => 20,
                'message' => 'Ввведите число. Для ввода дробного числа используйте символ "."(точка), например : 1.5 , 3.75 и т.п.'],
            [['width', 'height'], 'number', 'min' => 0.5, 'max' => 4,
                'message' => 'Ввведите число. Для ввода дробного числа используйте символ "."(точка), например : 1.5 , 3.75 и т.п.'],
            [['volume', 'volume_spec'], 'number', 'min' => 0.01, 'max' => 200,
                'message' => 'Ввведите число. Для ввода дробного числа используйте символ "."(точка), например : 1.5 , 3.75 и т.п.'],
            [['volume_spec'], 'number', 'min' => 0.1, 'max' => 5,
                'message' => 'Ввведите число. Для ввода дробного числа используйте символ "."(точка), например : 1.5 , 3.75 и т.п.'],
            [['photo'], 'string']
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios(); // TODO: Change the autogenerated stub
//        $scenarios[self::SCENARIO_UPDATE] = ['Price_zones'];
        $scenarios[self::SCENARIO_DELETE] = [];
        $scenarios[self::SCENARIO_CHECK] = ['error_mes'];
        $scenarios[self::SCENARIO_CREATE] = [];
        $scenarios[self::SCENARIO_UPDATE_TRUCK] = [
            'Price_zones','body_type', 'loadingTypeIds', 'tonnage', 'length'
            , 'width', 'height', 'volume', 'longlength'
        ];
        $scenarios[self::SCENARIO_UPDATE_PASS] = [
            'Price_zones','body_type', 'passengers'
        ];
        $scenarios[self::SCENARIO_UPDATE_SPEC_BODY_manipulator] = ['Price_zones', 'tonnage', 'length', 'width', 'tonnage_spec','length_spec'];
        $scenarios[self::SCENARIO_UPDATE_SPEC_BODY_dump] = ['Price_zones','tonnage', 'volume'];
        $scenarios[self::SCENARIO_UPDATE_SPEC_BODY_crane] = ['Price_zones', 'tonnage_spec','length_spec'];
        $scenarios[self::SCENARIO_UPDATE_SPEC_BODY_excavator] = ['Price_zones', 'volume_spec'];
        return $scenarios;
    }

    public function behaviors()
    {
        return [
            'convertDateTime' => [
                'class' => 'app\components\DateBehaviors',
                'dateAttributes' => ['update_at', 'create_at'],
                'format' => DateBehaviors::FORMAT_DATETIME,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID ТС'),
            'id_user' => Yii::t('app', 'ID пользователя'),
            'tonnage' => Yii::t('app', 'Грузо-подъемность (тонн)'),
            'length' => Yii::t('app', 'Длина кузова, (метры)'),
            'width' => Yii::t('app', 'Ширина кузова, (метры)'),
            'height' => Yii::t('app', 'Высота кузова, (метры)'),
            'volume' => Yii::t('app', 'Объем кузова, м3'),
            'passengers' => Yii::t('app', 'Количество пассажиров'),
            'tonnage_spec' => Yii::t('app', 'Грузо-подъемность механизма, (тонн)'),
            'length_spec' => Yii::t('app', 'Длина механизма, (метры)'),
            'volume_spec' => Yii::t('app', 'Объем механизма, (метры)'),
            'ep' => Yii::t('app', 'Количество "европоддонов" 0,8*1,2м'),
            'rp' => Yii::t('app', 'Количество "русских" поддонов 1,0*1,2м'),
            'lp' => Yii::t('app', 'Количество поддонов 1,2*1,2м'),
            'longlength' => 'Перевозка "длинномера"',
            'statusText' => 'Статус',
            'raiting' => 'Рейтинг',
            'body_type' => 'Тип кузова',
            'description' => 'Дополнительная информация',
            'loadingTypeIds' => 'Тип погрузки/выгрузки',
            'photoFile' => 'Фотография ТС',
            'priceZonesList' => 'Тарифные зоны.',
            'bodyTypeText' => 'Тип кузова',
            'loadingtypesText' => 'Тип погрузки / выгрузки',
            'create_at' => 'Дата создания',
            'update_at' => 'Дата редактирования',
            'longlengthIcon' => 'Груз-длинномер',
            'photoHtml' => 'Фотография ТС',
            'error_mes' => 'Причина отказа',


        ];
    }
//    public function getBodytypes()
//    {
//        return $this->hasMany(BodyType::className(), ['id' => 'id_bodytype'])
//            -> viaTable('XvehicleXtypebody', ['id_vehicle' => 'id']);
//    }
//    public function getVehicletypes()
//    {
//        return $this->hasMany(VehicleType::className(), ['id' => 'id_typevehicle'])
//            -> viaTable('XvehicleXtypevehicle', ['id_vehicle' => 'id']);
//    }

    public function beforeDelete()
    {
        $this->deleteFilePhoto();
        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }

    public function afterFind()
    {
        for ($i=0; $i<9; $i++){
//            $status = CalendarVehicle::STATUS_FREE;
            $status = null;
            $date = date('d.m.Y', time()  + (3600*24*$i));
//            $date = strtotime($date);
            $date = functions::DayToStartUnixTime($date);
            if($calendarVehicle = $this->getCalendarVehicle()->andWhere(['date' => $date])->one()){
                $status = $calendarVehicle->status;
            }
            $this->calendar[] = [
                'date' => $date,
                'status' => $status,
                'id_vehicle' => $this->id
            ];
        }
        parent::afterFind(); // TODO: Change the autogenerated stub
    }

    public function getCalendarVehicle($date = null){
        if($date){
            if(is_array($date)){

            } else{
                $date_unix_day = functions::DayToStartUnixTime($date);
                return $this->hasOne(CalendarVehicle::class, ['id_vehicle' => 'id'])
                    ->andWhere(['date' => $date_unix_day]);
            }
        } else {
            return $this->hasMany(CalendarVehicle::class, ['id_vehicle' => 'id']);
        }
    }

    public function getLoadingtypes()
    {
        return $this->hasMany(LoadingType::className(), ['id' => 'id_loadingtype'])
            ->viaTable('{{%XvehicleXlodingtype}}', ['id_vehicle' => 'id']);
    }
//    public function getClassifierVehicles()
//    {
//        return $this->hasMany(ClassifierVehicle::className(), ['id' => 'id_classifier_vehicle'])->viaTable('{{%XvehicleXclassifier_vehicle}}', ['id_vehicle' => 'id']);
//    }


    public function getPrice_zones()
    {
        return $this->hasMany(PriceZone::className(), ['id' => 'id_price_zone'])
            ->viaTable('XvehicleXpricezone', ['id_vehicle' => 'id'])
            ->andFilterWhere([PriceZone::tableName().'.status' => PriceZone::STATUS_ACTIVE])
            ;
    }

    public function hasPriceZone($id_pricezone){
        if (!$id_pricezone) return false;
        foreach ($this->getPrice_zones()->all() as $price_zone){
            if($price_zone->id == $id_pricezone) return true;
        }
        return false;
    }

    public function getIdRates()
    {
        return $this->hasMany(Vehicles::className(), ['id' => 'id_rate'])->viaTable('{{%XvehicleXrate}}', ['id_vehicle' => 'id']);
    }

    public function getRegLicense()
    {
        return $this->hasOne(RegLicense::className(), ['id' => 'reg_license_id']);

    }

    public function deleteFilePhoto()
    {
        $path = Yii::getAlias('@photo_vehicle/' . $this->photo);
        if (file_exists($path) && is_file($path)) {
            if (!unlink($path)) {
                return false;
            }
        }
        $this->photo = Setting::find()->one()->noPhotoPath;
        return true;
    }

    public function getStatusText()
    {
        switch ($this->status) {
            case self::STATUS_DELETED:
                return 'Удалено';
            case self::STATUS_ACTIVE:
                return 'Активно';
            case self::STATUS_NOT_ACTIVE:
                return 'Не активно';
            case self::STATUS_ONCHECKING:
                return 'На проверке';
            case self::STATUS_FULL_DELETED:
                return 'Удалено безвозвратно';
        }
    }

    public function getBodyTypeText($short = false, $html = false)
    {
        $body = BodyType::find()->where(['id' => $this->body_type])->one();
        $body_short = $body->body_short;
        if($html) $body_short = $body->getBodyShortWithTip();
        return ($short)
            ? $body_short
            : $body->body;
    }

    public function getBodyType()
    {
        return $this->hasOne(BodyType::className(), ['id' => 'body_type']);
    }
    //Выбранные тарифы
    public function getPriceZonesSelect(){
        return $this->hasMany(PriceZone::className(),['id' => 'id_price_zone'])
            ->viaTable('XvehicleXpricezone', ['id_vehicle' => 'id'])
            ->andWhere([PriceZone::tableName().'.status' => PriceZone::STATUS_ACTIVE]);
    }
// Подходящие тарифы
    public static function getPriceZones($modelVehicle, $idVehicleType)
    {
        $result = [];
        $priceZones = PriceZone::find()->where(['veh_type' => $idVehicleType])
            ->andWhere(['status' => PriceZone::STATUS_ACTIVE]);
        switch ($idVehicleType) {
            case Vehicle::TYPE_TRUCK:
                $priceZones = $priceZones
                    ->andFilterWhere(['<=', 'tonnage_min', $modelVehicle->tonnage])
//                    ->andFilterWhere(['<=', 'volume_min', $modelVehicle->volume])
//                    ->andFilterWhere(['<=', 'length_min', $modelVehicle->length])
                    ->andFilterWhere(
                        ['or' ,
                            ['<=', 'volume_min', $modelVehicle->volume],
                            ['<=', 'length_min', $modelVehicle->length]
                        ]
                    )
                ;
//                (!$modelVehicle->longlength)
//                    ? $priceZones = $priceZones->andFilterWhere(['longlength' => $modelVehicle->longlength])->orderBy(['r_km'=>SORT_DESC, 'r_h'=>SORT_DESC])->all()
//                    : $priceZones = $priceZones->orderBy(['r_km'=>SORT_DESC, 'r_h'=>SORT_DESC])->all();
                if(!$modelVehicle->longlength){
                    $priceZones = $priceZones
                        ->andFilterWhere(['longlength' => $modelVehicle->longlength])
//                        ->andFilterWhere(['<=', 'length_min', $modelVehicle->length])
                        ->andFilterWhere(
                            ['or' ,
                                ['<=', 'volume_min', $modelVehicle->volume],
                                ['<=', 'length_min', $modelVehicle->length]
                            ]
                        )
                        ->orderBy(['r_km'=>SORT_DESC, 'r_h'=>SORT_DESC])
                        ->all()
                    ;
                } else {
                    $priceZones = $priceZones
                        ->andFilterWhere([
                            'or',
                            ['and',
                                ['longlength' => false],
                                ['<=', 'length_min', $modelVehicle->length]
                            ],
                            ['and',
                                ['longlength' => true],
                                ['<=', 'length_min', ($modelVehicle->length + 2)],
                            ]
                        ])
                        ->orderBy(['r_km' => SORT_DESC, 'r_h' => SORT_DESC])
                        ->all();
                }
                break;
            case Vehicle::TYPE_PASSENGER:
                $priceZones = $priceZones
                    ->andFilterWhere(['<=', 'passengers', $modelVehicle->passengers])
                    ->orderBy(['r_km'=>SORT_DESC, 'r_h'=>SORT_DESC])
                    ->all()
                ;
                break;
            case Vehicle::TYPE_SPEC:
                switch ($modelVehicle->body_type){
                    case Vehicle::BODY_manipulator:
                        $priceZones = $priceZones
                            ->andFilterWhere(['<=', 'tonnage_spec_min', $modelVehicle->tonnage_spec])
                            ->andFilterWhere(['<=', 'length_spec_min', $modelVehicle->length_spec])
                            ->andFilterWhere(['<=', 'tonnage_min', $modelVehicle->tonnage])
                            ->andFilterWhere(['<=', 'length_min', $modelVehicle->length])
                        ;
                        break;
                    case Vehicle::BODY_dump:
                        $priceZones = $priceZones
                            ->andFilterWhere(['<=', 'tonnage_min', $modelVehicle->tonnage])
                            ->andFilterWhere(['<=', 'volume_min', $modelVehicle->volume])
                        ;
                        break;
                    case Vehicle::BODY_crane:
                        $priceZones = $priceZones
                            ->andFilterWhere(['<=', 'tonnage_spec_min', $modelVehicle->tonnage_spec])
                            ->andFilterWhere(['<=', 'length_spec_min', $modelVehicle->length_spec])
                        ;
                        break;
                    case Vehicle::BODY_excavator:
                        $priceZones = $priceZones
                            ->andFilterWhere(['<=', 'volume_spec', $modelVehicle->volume_spec])
                        ;
                        break;
                    case Vehicle::BODY_excavator_loader:
                        $priceZones = $priceZones
                            ->andFilterWhere(['<=', 'volume_spec', $modelVehicle->volume_spec])
                        ;
                        break;
                }
                $priceZones = $priceZones
                    ->orderBy(['r_km'=>SORT_DESC, 'r_h'=>SORT_DESC])
                    ->all()
                ;
                break;
        }

//        $priceZones = $priceZones->all();
            foreach ($priceZones as $priceZone) {
                if ($priceZone->hasBodyType($modelVehicle->body_type))
                    $result[$priceZone->id] = 'Тарифная зона ' . $priceZone->id;
            }
        return $result;
    }

    public function getBrand(){
        return ($this && $this->regLicense)?
            $this->regLicense->brand:
            null;
    }

    public function getLoadingtypesText($short = false){
        if(!$this->loadingtypes)return null;
        $LTypies = '';
        foreach ($this->loadingtypes as $loadingtype){
            $LTypies .= ($short)
                ? $loadingtype->type_short . ' '
                : $loadingtype->type . ' ';
        }
        return $LTypies;

    }

    public function getProfile(){
        return $this->hasOne(Profile::className(), ['id_user' => 'id_user']);
    }

    public function getUser(){
        return $this->hasOne(User::className(), ['id' => 'id_user']);
    }

    public function getLonglengthIcon(){
        return
            ($this->longlength)?
                '<span class="glyphicon glyphicon-ok-circle"></span>':
                '<span class="glyphicon glyphicon-remove-circle"></span>'
            ;
    }

    public function getPhotoHtml(){
        return ($this->photo)?
            Html::a(Html::img(
            '/uploads/photos/vehicles/'.$this->photo, ['class' => 'profile_photo_min']),
                '/uploads/photos/vehicles/'.$this->photo, ['target' => 'blank']) :
        Html::img('/img/noPhoto.jpg', ['class' => 'profile_photo_min']);
    }

    public function getPriceZonesList()
    {
        $return = '';
        foreach ($this->price_zones as $price_zone) {
            $return .= Html::a($price_zone->id, Url::to(['/price-zone/view', 'id' => $price_zone->id]), ['target' => 'blank']);
            $return .= ', ';
        }
        $return = substr($return, 0, -2);
        return $return;
    }

    public function getIdsListWithModalInfo()
    {
        $return = '';
        foreach ($this->price_zones as $price_zone) {
            $price_zone = $price_zone->getPriceZoneForCarOwner($this->id_user);
            $return .= ShowMessageWidget::widget([
                'ToggleButton' => [
                    'label' =>$price_zone->id,
                    'class' => 'btn btn-sm'
                ],
                'helpMessage' => $price_zone->printHtml()
            ]);
            $return .= ' ';
        }
//        $return = substr($return, 0, -2);
        return $return;
    }

    public function getOrders(){
        return $this->hasMany(Order::class, ['id_vehicle'=>'id']);
    }

    public function  hasOrder($statuses_orders) : bool{
        $orders = $this->orders;
        if(!$statuses_orders || !$orders) return false;

        foreach ($orders as $order){
            if(!is_array($statuses_orders)){
                if($order->status == $statuses_orders) return true;
            } else {
                foreach ($statuses_orders as $statuse_order){
                    if($order->status == $statuse_order) return true;
                }
            }
        }
        return false;
    }
// Массив атрибутов для разных типов транспота и типов кузовов
    static public function getArrayAttributes($id_vehicle_type, $TYPE_BODY){
        $res = [];
        switch ($id_vehicle_type){
            case self::TYPE_TRUCK;
                $res = ['tonnage', 'length', 'width', 'height', 'volume', 'ep', 'rp', 'lp','passengers'];
                break;
            case self::TYPE_PASSENGER:
                $res = ['passengers'];
                break;
            case self::TYPE_SPEC:
                switch ($TYPE_BODY[1]){
                    case self::BODY_manipulator:
                        $res = ['tonnage','length', 'width', 'tonnage_spec', 'length_spec'];
                        break;
                    case self::BODY_dump:
                        $res = ['tonnage', 'volume'];
                        break;
                    case self::BODY_crane:
                        $res = ['tonnage_spec', 'length_spec'];
                        break;
                    case self::BODY_excavator:
                        $res = ['volume_spec'];
                        break;
                    case self::BODY_excavator_loader:
                        $res = ['volume_spec'];
                        break;
                }
                break;
        }

        return $res;
    }

    public function canOrder(Order $Order, $withPriceZone = true){
        if ($this->id_vehicle_type != $Order->id_vehicle_type) return false;

        $hasPriceZone = 0;
        foreach ($Order->priceZones as $priceZone){
            foreach ($this->priceZonesSelect as $pZone){
                if ($priceZone->id == $pZone->id) $hasPriceZone = 1;
            }
        }
        if(!$withPriceZone){
            $hasPriceZone = 1;
        }
        if((!$hasPriceZone || !$Order->hasBodyType($this->bodyType))
            && !$Order->re) return false;

        switch ($this->id_vehicle_type){
            case Vehicle::TYPE_TRUCK:
                if(
                    $this->tonnage >= $Order->tonnage
//                    && $this->length >= $Order->length
                    && $this->height >= $Order->height
                    && $this->width >= $Order->width
                    && $this->passengers >=$Order->passengers
                    && $this->volume >= $Order->volume
                    && $this->hasLoadingTypies($Order->loadingTypies)
                ){
                    if($Order->longlength){
                        if($this->longlength){
                            if(($this->length + 2) >= $Order->length) return true;
                        } else {
                            if($this->length >= $Order->length) return true;
                        }
                    } else{
                        if($this->length >= $Order->length) return true;
                    }
                }

                break;
            case Vehicle::TYPE_PASSENGER:
                if(
                    $this->passengers >= $Order->passengers
                    && $this->tonnage >= $Order->tonnage
                )
                    return true;
                break;
            case Vehicle::TYPE_SPEC:
                switch ($this->body_type){
                    case Vehicle::BODY_dump:
                        if(
                            $this->tonnage >= $Order->tonnage
                            &&$this->volume >= $Order->volume
                        )
                            return true;
                        break;
                    case Vehicle::BODY_crane:
                        if(
                            $this->tonnage_spec >= $Order->tonnage_spec
                            && $this->length_spec >= $Order->length_spec
                        )
                            return true;
                        break;
                    case Vehicle::BODY_manipulator:
//                        if(
//                            $this->tonnage >= $Order->tonnage
//                            && $this->length >= $Order->length
//                            && $this->tonnage_spec >= $Order->tonnage_spec
//                            && $this->length_spec >= $Order->length_spec
//                        )
                            return true;
                        break;
                    case Vehicle::BODY_excavator:
                        if(
                            $this->volume_spec >= $Order->volume_spec
                        )
                            return true;
                        break;
                    case Vehicle::BODY_excavator_loader:
                        if(
                            $this->volume_spec >= $Order->volume_spec
                        )
                            return true;
                        break;
                }
                break;
            default:
                return false;
        }
        return false;
    }
    //ТС имеет все заданные виды погрузки или нет
    public function hasLoadingTypies($loadingTypies){
        foreach ($loadingTypies as $loadingType){
            $hasLoadingType = 0;
            foreach ($this->loadingtypes as $lType){
                if ($loadingType->id == $lType->id) $hasLoadingType = 1;
            }
            if(!$hasLoadingType) return false;
        }
        return true;
    }

    public function getMinRate(Order $Order = null){
        if(!$Order || !$this->canOrder($Order)){
            $PriceZones = $this->priceZonesSelect;
            $tmpPriceZone = new PriceZone();
            $tmpPriceZone->attributes = $PriceZones[0]->attributes;
            $cost_r = $tmpPriceZone->r_km;
            $cost_h = $tmpPriceZone->r_h;

            foreach ($this->priceZonesSelect as $priceZone){
                if($cost_r > $priceZone->r_km || $cost_h > $priceZone->r_h) {
                    $cost_r = $priceZone->r_km;
                    $cost_h = $priceZone->r_h;
//                $id_rate = $priceZone->id;
                    $tmpPriceZone->attributes = $priceZone->attributes;
                }
            }
            return ($tmpPriceZone) ? $tmpPriceZone : null;
        }
//        if(!$this->canOrder($Order)) return null;
        $pricezonesForVehicle = [];
        foreach ($Order->priceZones as $OrderPriceZone) {
            foreach ($this->priceZonesSelect as $priceZone){
                if($OrderPriceZone->id == $priceZone->id){
                    $pricezonesForVehicle[] = $OrderPriceZone;
                }
            }
        }
        if(!$pricezonesForVehicle) return false;
        $tmpPriceZone = new PriceZone();
        $tmpPriceZone->attributes = $pricezonesForVehicle[0]->attributes;
        $cost_r = $tmpPriceZone->r_km;
        $cost_h = $tmpPriceZone->r_h;
//        $id_rate = $tmpPriceZone->id;
        foreach ($pricezonesForVehicle as $priceZone) {

            if($cost_r > $priceZone->r_km || $cost_h > $priceZone->r_h) {
                $cost_r = $priceZone->r_km;
                $cost_h = $priceZone->r_h;
//                $id_rate = $priceZone->id;
                $tmpPriceZone->attributes = $priceZone->attributes;
            }
        }
        return ($tmpPriceZone) ? $tmpPriceZone : null;
//        return ($id_rate) ? $id_rate : false;
    }

    public function getBrandAndNumber(){
        return $this->brand . ' ' . $this->regLicense->reg_number;
    }

    public function getFullInfo(){
        $return = '<b>'.$this->brandAndNumber . '</b><br>';
        $return .= 'Тип кузова: ' . $this->bodyTypeText . '. ';

        switch ($this->id_vehicle_type){
            case self::TYPE_TRUCK;
                $return .= 'Тип погрузки/выгрузки: ' . $this->loadingtypesText . '. ';
                $return .= 'Грузоподъемность: ' . $this->tonnage . 'т. ';
                $return .= 'Размеры (Д*Ш*В): ' . $this->length . ' * ' . $this->width . ' * ' . $this->height  .'м. ';
                $return .= 'Объем: ' . $this->volume . 'м3. ';
                if($this->ep)
                    $return .= 'Евро-поддоны(1.2*0.8м): ' . $this->ep . 'шт, ';
                if($this->rp)
                    $return .= 'поддоны(1.2*1м): ' . $this->rp . 'шт, ';
                if($this->lp)
                    $return .= 'поддоны(1.2*1.2м): ' . $this->lp . 'шт. ';
                if($this->passengers)
                    $return .= 'Пассажиры: ' . $this->passengers . 'чел. ';
                $return .= 'Груз-длинномер: ' . $this->longlengthIcon . '. <br>';
                break;
            case self::TYPE_PASSENGER:
                $return .= 'Пассажиры: ' . $this->passengers . 'чел. ';
                $return .= 'Грузоподъемность: ' . $this->tonnage . 'т. ';
                break;
            case self::TYPE_SPEC:
                switch ($this->body_type){
                    case self::BODY_manipulator:
                        $return .= 'Грузоподъемность: ' . $this->tonnage . 'т. ';
                        $return .= 'Размеры (Д*Ш): ' . $this->length . ' * ' . $this->width  .'м. ';
                        $return .= 'Грузоподъемность механизма (стрелы): ' . $this->tonnage_spec . 'т. ';
                        $return .= 'Длина механизма (стрелы): ' . $this->length_spec . 'м. ';
                        break;
                    case self::BODY_dump:
                        $return .= 'Грузоподъемность: ' . $this->tonnage . 'т. ';
                        $return .= 'Объем: ' . $this->volume . 'м3. ';
                        break;
                    case self::BODY_crane:
                        $return .= 'Грузоподъемность механизма (стрелы): ' . $this->tonnage_spec . 'т. ';
                        $return .= 'Длина механизма (стрелы): ' . $this->length_spec . 'м. ';
                        break;
                    case self::BODY_excavator:
                        $return .= 'Объем механизма (ковша): ' . $this->volume_spec . 'м3. ';
                        break;
                    case self::BODY_excavator_loader:
                        $return .= 'Объем механизма (ковша): ' . $this->volume_spec . 'м3. ';
                        break;
                }
                break;
        }
        $return .= $this->description;
        return $return;
    }

    public function hasLoadingType($id_lType) : bool {
        foreach ($this->loadingtypes as $loadingType){
            if($loadingType->id == $id_lType) return true;
        }
        return false;
    }

    public function getVehicleProcentPrice(){
        // Пока что у всех 9%
        return \app\models\setting\SettingVehicle::find()->limit(1)->one()->procent_vehicle;
    }

    static public function notAdminOrDispetcher(){
        $user = Yii::$app->user;
        if($user->can('admin') || $user->can('dispetcher')) return false;
        return true;
    }

    public function hasOrderOnDate($date, bool $icon = false){
        $unix_date = functions::DayToStartUnixTime($date);
        $orders = $this->getOrders()
            ->where(['between', 'datetime_start', time()-3600*24*2, time()+3600*24*10])
            ->andWhere(['in', 'status',[ Order::STATUS_IN_PROCCESSING, Order::STATUS_VEHICLE_ASSIGNED]])
            ->all();
        foreach ($orders as $order){
            $date_start_unix = functions::DayToStartUnixTime($order->datetime_start);
            if($date_start_unix == $unix_date) {
                if($route = $order->route){
                    $result = $route->distance;
                    if($icon){
                        if($route->distance>120){
                            $result = Html::icon('glyphicon glyphicon-ban-circle');
                        } else {
                            $result = Html::icon('glyphicon glyphicon-adjust');
                        }
                    }
                    return $result;
                }
                return true;
            };
        }
        return false;
    }

    static public function ArrayCalendarParamsForRender($id_user){
        if($id_user){
            $user = User::findOne($id_user);
        } else {
            $user = Yii::$app->user->identity;
        }

        $vehicles = $user->getVehicles()
            ->andWhere(['in', 'status', [Vehicle::STATUS_ACTIVE, Vehicle::STATUS_ONCHECKING]])
            ->all();
        $Vehicles = [];
        $ids_vehicles = '';
        if($vehicles) {
            foreach ($vehicles as $vehicle) {
                $ids_vehicles .= $vehicle->id . ' ';
                $calendar = $vehicle->calendar;
                $Vehicles [$vehicle->id] = new ArrayDataProvider([
                    'allModels' => $calendar
                ]);
            }
            $ids_vehicles = substr($ids_vehicles, 0, -1);
        }
        return [
            'Vehicles' => $Vehicles,
            'ids_vehicles' => $ids_vehicles
        ];
    }

    public function removePriceZone($id_price_zone){
        $price_zone = PriceZone::findOne(['id' => $id_price_zone, 'status' => PriceZone::STATUS_ACTIVE]);
        if($price_zone){
           $this->unlink('price_zones', $price_zone, true);
        }
    }

}