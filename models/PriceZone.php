<?php

namespace app\models;

use app\components\SerializeBehaviors;
use app\models\setting\SettingVehicle;
use function Couchbase\fastlzCompress;
use Yii;
use app\components\DateBehaviors;
use yii\bootstrap\Html;
use app\components\widgets\ShowMessageWidget;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "price_zone".
 * @property integer unique_index
 * @property integer $id
 * @property integer $veh_type
 * @property string $body_types
 * @property integer $longlength
 * @property double $tonnage_min
 * @property double $tonnage_max
 * @property double $volume_min
 * @property double $volume_max
 * @property double $length_min
 * @property double $length_max
 * @property double $tonnage_long_min
 * @property double $tonnage_long_max
 * @property double $length_long_min
 * @property double $length_long_max
 * @property integer $passengers
 * @property double $tonnage_spec_min
 * @property double $tonnage_spec_max
 * @property double $length_spec_min
 * @property double $length_spec_max
 * @property double $volume_spec
 * @property double $r_km
 * @property double $h_loading
 * @property integer $r_loading
 * @property double $min_price
 * @property double $r_h
 * @property double $min_r_10
 * @property double $min_r_20
 * @property double $min_r_30
 * @property double $min_r_40
 * @property double $min_r_50
 * @property double $remove_awning
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string history
 * @property string bodiesColumn

 */
class PriceZone extends \yii\db\ActiveRecord
{
    const SCENARIO_NULL = 'NULL';

    const SCENARIO_TRUCK = 'truck';
    const SCENARIO_PASS = 'pass';
    const SCENARIO_SPEC = 'spec';

    const SCENARIO_MANIPULATOR = 'manipulator';
    const SCENARIO_CRANE = 'crane';
    const SCENARIO_EXCAVATOR = 'excavator';
    const SCENARIO_DUMP = 'dump';

    const STATUS_NEW = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_OLD = 2;

    const SORT_TRUCK = [
        'enableMultiSort' => true,
        'attributes' => [
            'longLength',
            'tonnage_max',
            'tonnage_min',
            'length_max',
            'length_min',
            'volume_max',
//            'length_long_max',
//            'tonnage_long_max'
        ],
        'defaultOrder' => [
            'tonnage_max' => SORT_ASC,
            'length_max' => SORT_ASC,
            'volume_max' => SORT_ASC,
            'tonnage_min' => SORT_ASC,
            'length_min' => SORT_ASC,
            'longLength' => SORT_ASC,

//            'length_long_max' => SORT_ASC,
//            'tonnage_long_max' => SORT_ASC
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
            'body_types',
            'tonnage_min',
            'length_min',
            'tonnage_spec_min',
            'length_spec_min',
            'volume_spec'
        ],
        'defaultOrder' => [
            'body_types'  => SORT_ASC,
            'tonnage_min' => SORT_ASC,
            'length_min' => SORT_ASC,
            'tonnage_spec_min' => SORT_ASC,
            'length_spec_min' => SORT_ASC,
            'volume_spec' => SORT_ASC,
        ]
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'price_zone';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'tonnage_min', 'tonnage_max', 'volume_min', 'volume_max', 'length_min', 'length_max', 'longlength', 'passengers',
                'tonnage_spec_min', 'tonnage_spec_max', 'length_spec_min', 'length_spec_max', 'volume_spec', 'r_km', 'r_loading',
                'h_loading', 'min_price', 'r_h', 'min_r_10', 'min_r_20', 'min_r_30', 'min_r_40', 'min_r_50', 'remove_awning'], 'required'],
            [['veh_type', 'body_types'], 'required', 'message' => 'Выберите хотя бы один из вариантов'],
            [['unique_index', 'tonnage_min', 'tonnage_max', 'volume_min', 'volume_max', 'length_min', 'length_max',
                'tonnage_long_min', 'tonnage_long_max', 'length_long_min', 'length_long_max',
                'tonnage_spec_min', 'tonnage_spec_max', 'length_spec_min', 'length_spec_max', 'volume_spec', 'r_km', 'r_loading',
                'h_loading', 'min_price', 'r_h', 'min_r_10', 'min_r_20', 'min_r_30', 'min_r_40', 'min_r_50', 'remove_awning'], 'number'],
            [[
                'tonnage_min', 'tonnage_max', 'volume_min', 'volume_max', 'length_min', 'length_max', 'tonnage_long_min', 'tonnage_long_max',
                'length_long_min', 'length_long_max', 'tonnage_spec_min', 'tonnage_spec_max', 'length_spec_min', 'length_spec_max', 'volume_spec',
            ], 'default', 'value' => null],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            [['created_at', 'updated_at'], 'default','value' => date('d.m.Y H:m')],
            [['bodiesColumn','longlength','remove_awning', 'created_at', 'updated_at', 'history'], 'safe'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios(); // TODO: Change the autogenerated stub
        $scenarios[self::SCENARIO_TRUCK] = [
            'id', 'body_types', 'tonnage_min', 'tonnage_max', 'volume_min', 'volume_max', 'length_min', 'length_max',
            'longlength', 'r_km', 'r_loading', 'status', 'updated_at', 'creates_at',
            'h_loading', 'min_price', 'r_h', 'min_r_10', 'min_r_20', 'min_r_30', 'min_r_40', 'min_r_50', 'remove_awning'
        ];
        $scenarios[self::SCENARIO_PASS] = [
            'id', 'body_types', 'passengers', 'r_km', 'r_loading', 'status', 'updated_at', 'creates_at',
            'h_loading', 'min_price', 'r_h', 'min_r_10', 'min_r_20', 'min_r_30', 'min_r_40', 'min_r_50'
        ];
        $scenarios[self::SCENARIO_MANIPULATOR] = [
            'id', 'tonnage_min', 'tonnage_max',  'length_min', 'length_max', 'status', 'updated_at', 'creates_at',
            'tonnage_spec_min', 'tonnage_spec_max', 'length_spec_min', 'length_spec_max', 'r_loading',
            'h_loading', 'min_price', 'r_h', 'min_r_10', 'min_r_20', 'min_r_30', 'min_r_40', 'min_r_50'
        ];
        $scenarios[self::SCENARIO_CRANE] = [
            'id', 'tonnage_spec_min', 'tonnage_spec_max', 'length_spec_min', 'length_spec_max','r_km', 'r_loading',
            'h_loading', 'min_price', 'r_h', 'min_r_10', 'min_r_20', 'min_r_30', 'min_r_40', 'min_r_50', 'status', 'updated_at', 'creates_at'
        ];
        $scenarios[self::SCENARIO_DUMP] = [
            'id', 'tonnage_min', 'tonnage_max', 'volume_min', 'volume_max',  'r_km', 'r_loading', 'status', 'updated_at', 'creates_at',
            'h_loading', 'min_price', 'r_h', 'min_r_10', 'min_r_20', 'min_r_30', 'min_r_40', 'min_r_50'
        ];
        $scenarios[self::SCENARIO_EXCAVATOR] = [
            'id', 'volume_spec', 'r_km', 'r_loading', 'status', 'updated_at', 'creates_at',
            'h_loading', 'min_price', 'r_h', 'min_r_10', 'min_r_20', 'min_r_30', 'min_r_40', 'min_r_50'
        ];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'veh_type' => 'Тип транспорта',
            'body_types' => 'Типы кузова',
            'bodiesColumn' => 'Типы кузова',
            'longlength' => 'Груз-длинномер',
            'tonnage_min' => 'Грузоподъемность мин. (т.)',
            'tonnage_max' => 'Грузоподъемность макс. (т.)',
            'volume_min' => 'Объем мин. (м3)',
            'volume_max' => 'Объем макс. (м3)',
            'length_min' => 'Длина кузова мин. (м.)',
            'length_max' => 'Длина кузова макс. (м.)',
            'tonnage_long_min' => 'Грузоподъемность при перевозке длинномера минимальная',
            'tonnage_long_max' => 'Грузоподъемность при перевозке длинномера максимальная',
            'length_long_min' => 'Длина длинномера мин.',
            'length_long_max' => 'Длина длинномера макс.',
            'passengers' => 'Кол-во пассажиров',
            'tonnage_spec_min' => 'Грузоподъемность механизма (стрелы) мин. (т.)',
            'tonnage_spec_max' => 'Грузоподъемность механизма (стрелы) макс. (т.)',
            'length_spec_min' => 'Длина механизма (стрелы) мин. (м.)',
            'length_spec_max' => 'Длина механизма (стрелы) макс. (м.)',
            'volume_spec' => 'Объем механизма (ковша) (м3)',
            'r_km' => 'Руб/км (при пробеге более 120 км)',
            'h_loading' => 'Бесплатное время на погрузку/ разгрузку/ ожидание при пробеге >120км, ч.',
            'r_loading' => 'Переработка сверх бесплатного времени при пробеге >120км, руб/час',
            'min_price' => 'Мин. плата при пробеге >120км, руб.',
            'r_h' => 'Руб/час (при пробеге менее 120 км)',
            'min_r_10' => 'Мин. плата при пробеге <20км, ч.',
            'min_r_20' => 'Мин. плата при пробеге >20км, ч.',
            'min_r_30' => 'Мин. плата при пробеге >40км, ч.',
            'min_r_40' => 'Мин. плата при пробеге >60км, ч.',
            'min_r_50' => 'Мин. плата при пробеге >80км и <120 км, ч.',
            'remove_awning' => 'Растентовка (1 сторона), руб.',
            'status' => 'Статус',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения',
            'history' => 'История изменений',
        ];
    }

    public function behaviors()
    {
        return [
            'convertDate' => [
                'class' => 'app\components\DateBehaviors',
                'dateAttributes' => ['created_at', 'updated_at'],
                'format' => DateBehaviors::FORMAT_DATETIME,
            ],
            'serializeUnserialize' => [
                'class' => 'app\components\SerializeBehaviors',
                'arrAttributes' => ['body_types'],
            ]
        ];
    }


    public function getBodyTypies(){
        if($this->body_types){
            return BodyType::find()->where(['in', 'id', $this->body_types])->all();
        } return false;
    }

    public function getBodiesColumn(){
        if(!$this->bodyTypies) return false;
        $stringBodies = '<ul>';
        foreach ($this->bodyTypies as $bType){
            $stringBodies .= '<li>' . $bType->body . '</li>';
        }

        $stringBodies .= '</ul>';
        return $stringBodies;
    }

    public function getBodyTypiesText($short = true, $html = true){
        if(!$this->bodyTypies) return false;
        $stringBodies = '';
        foreach ($this->bodyTypies as $bType){
            $body_short = $bType->body_short;
            if($html && $short) $body_short = $bType->getBodyShortWithTip();
            $stringBodies .= ($short)
                ? $body_short
                : $bType->body;
            $stringBodies .= ', ';
        }

        return $stringBodies;
    }

    public function printHtml(){
        $result =
            '<p>' . $this->r_km . ' руб/км при пробеге более 120км</p>'
            . '<p>' . $this->h_loading . ' ч. - на погрузку/выгрузку/ожидание при пробеге более 120км </p>'
            . '<p>' . $this->r_loading . ' руб/ч - при превышении времени на погрузку/выгрузку *</p>'
            . '<p>' . $this->min_price . ' руб. - минимальная оплата при пробеге более 120км </p>'
            . '<br>'
            . '<p>' . $this->r_h . ' руб/час - при пробеге менее 120км </p>'
            . '<p>' . $this->min_r_10 . ' ч. - минимальная оплата при пробеге менее 20км</p>'
            . '<p>' . $this->min_r_20 . ' ч. - минимальная оплата при пробеге более 20км </p>'
            . '<p>' . $this->min_r_30 . ' ч. - минимальная оплата при пробеге более 40км </p>'
            . '<p>' . $this->min_r_40 . ' ч. - минимальная оплата при пробеге более 60км </p>'
            . '<p>' . $this->min_r_50 . ' ч. - минимальная оплата при пробеге более 80км </p>'
            . '<br>'
            . '<p>' . $this->remove_awning . ' руб. - за "растентовку" одной стороны при технической возможности </p>'
            . '<br>'
            . '<p>Это основной тариф для: </p>'
            ;


        switch ($this->veh_type) {
            case Vehicle::TYPE_TRUCK:
                if ($this->longlength) {
                    $result .=
                        '<p>Груз-длинномер (выход за габариты кузова по длинне до 2-х метров со знаком). </p>';

                }
                $result .=
                    '<p>' . $this->tonnage_min . ' - ' . $this->tonnage_max . 'т. - Грузоподъемность </p>'
                    . '<p>' . $this->length_min . ' - ' . $this->length_max . 'м. - Длина кузова </p>'
                    . '<p>' . $this->volume_min . ' - ' . $this->volume_max . 'м3. - объем кузова </p>';
                break;

            case Vehicle::TYPE_PASSENGER:
                $result .= '<p>Количество пассажиров: ' . $this->passengers . '.</p>';
                break;

            case Vehicle::TYPE_SPEC:
                switch ($this->body_types[0]) {
                    case Vehicle::BODY_manipulator:
                        $result .=
                            '<p>' . $this->tonnage_min . ' - ' . $this->tonnage_max . 'т. - Грузоподъемность </p>'
                            . '<p>' . $this->length_min . ' - ' . $this->length_max . 'м. - Длина кузова </p>';
                        break;
                    case Vehicle::BODY_dump:
                        $result .= '<p>' . $this->volume_min . ' - ' . $this->volume_max . 'м3. - объем кузова </p>';
                        break;
                    case Vehicle::BODY_crane:
                        $result .=
                            '<p>' . $this->tonnage_spec_min . ' - ' . $this->tonnage_spec_max . 'т. - Грузоподъемность </p>'
                            . '<p>' . $this->length_spec_min . ' - ' . $this->length_spec_max . 'м. - Длина кузова </p>';
                        break;
                    case Vehicle::BODY_excavator:
                    case Vehicle::BODY_excavator_loader:
                        $result .= '<p>' . $this->volume_min . ' - ' . $this->volume_spec . 'м3. - объем кузова </p>';
                        break;
                }
                break;
        }
        $result .=
            'Типы кузовов: '
            .'<p>' . $this->getBodiesColumn() . '</p>';
        return $result;
    }

    public function hasBodyType($bodyType){
        if(!$bodyType)return false;
        foreach ($this->body_types as $body_type){
            if($body_type == $bodyType) return true;
        }
        return false;
    }

    public function CostCalculation($distance, $discount = null){
        if($distance){
            $cost = 0;
                if($distance < 20){
                    $cost = $this->min_r_10 * $this->r_h;
                }
                else if ($distance >= 20 && $distance<40){
                    $cost = $this->min_r_20 * $this->r_h;
                }
                else if ($distance >= 40 && $distance<60){
                    $cost = $this->min_r_30 * $this->r_h;
                }
                else if ($distance >= 60 && $distance<80){
                    $cost = $this->min_r_40 * $this->r_h;
                }
                else if ($distance >= 80 && $distance<120){
                    $cost = $this->min_r_50 * $this->r_h;
                }
                else if ($distance >= 120) {
                    if ($this->veh_type == Vehicle::TYPE_SPEC && !$this->r_km) {
                        return 'Цена договорная. Обсуждается с водителем.';
                    } else {
                        $cost = $this->r_km * $distance;
                        if ($cost < $this->min_price) $cost = $this->min_price;
                    }
                }
            return round($cost - ($cost*$discount/100));
        }
        return 0;
    }

    public function CostCalculationWithDiscountHtml($distance = null, $discount = null){
        $cost = $this->CostCalculation($distance);
        if(!$cost) return 'Невозможно расчитать стоимость. Расстояние не определено!';
        if(!$discount) return $cost;
        $return = '<s>'. $cost . '</s> '
            . '<strong>' . round($cost - ($cost*$discount/100)) . '</strong>';

        return $return;

    }

    public function getTextWithShowMessageButton($distance = null, bool $infoButton = true, $discount = null){
        $return = '';
        if($discount){
            $cost = '<s>'. $this->CostCalculation($distance) . '</s> '
                . '<strong>' . round($this->CostCalculation($distance, $discount)) . '</strong>';
            $r_km = '<s>'.  $this->r_km . '</s> '
                . '<strong>' . round($this->r_km - ( $this->r_km*$discount/100),1) . '</strong>';
            $r_h = '<s>'. $this->r_h . '</s> '
                . '<strong>' . round($this->r_h - ($this->r_h*$discount/100)) . '</strong>';
        } else{
            $cost = $this->CostCalculation($distance);
            $r_km =  $this->r_km;
            $r_h = $this->r_h ;
        }
//        return $cost;

        $return .= 'Тариф №' . $this->id . '. ';
        if($distance)$return .= '(&asymp;' . $cost . 'р.*) ';
        $return .= '<i style="font-size: x-small; font-style: italic">'
            . $r_km . ' р/км '
            . ', '
            . $r_h . ' р/час...)';
        if($infoButton){
            $return .= ShowMessageWidget::widget([
                    'helpMessage' => $this->printHtml(),
                    'header' => 'Тарифная зона ' . $this->id,
                    'ToggleButton' => ['label' => Html::icon('info-sign'), 'class' => 'btn']
                ]) . '</i>';
        }

        return $return;
    }

    public function getLabelWithShowMessageButton($distance = null){
        $return = '';
//        $return .= 'Тариф №' . $this->id;
        if($distance)$return .= '&asymp;' . $this->CostCalculation($distance) . 'р.* '
            . '<i style="font-size: x-small; font-style: italic">'
            . '(Тариф №' . $this->id . '. '
            . $this->r_km . ' р/км '
            . ', '
            . $this->r_h . ' р/час...)';
        $return .= ShowMessageWidget::widget([
                'helpMessage' => $this->printHtml(),
                'header' => 'Тарифная зона ' . $this->id,
                'ToggleButton' => ['label' => Html::icon('info-sign'), 'class' => 'btn']
            ]) . '</i>';

        return $return;
    }

    public function getPriceAndShortInfo($distance = null){
        $return = '';
        if($distance)$return .= '&asymp;' . $this->CostCalculation($distance) . 'р. ';
        $return .= '<p style="font-size: x-small; font-style: italic">'
            . '(Тариф №' . $this->id . '. '
            . $this->r_km . ' р/км '
            . ', '
            . $this->r_h . ' р/час...)</p>';
        return $return ;
    }

    public function getWithDiscount($discount = 0) : PriceZone{
        if(!$discount) return $this;
        $returnPZ = new PriceZone();
        $returnPZ->attributes = $this->attributes;

        $returnPZ->r_km = self::mathDiscount($this->r_km, $discount);
        $returnPZ->r_h = self::mathDiscount($this->r_h, $discount);
        $returnPZ->r_loading = self::mathDiscount($this->r_loading, $discount);
        $returnPZ->min_price = self::mathDiscount($this->min_price, $discount);
        $returnPZ->min_r_10 = $this->min_r_10;
        $returnPZ->min_r_20 = $this->min_r_20;
        $returnPZ->min_r_30 = $this->min_r_30;
        $returnPZ->min_r_40 = $this->min_r_40;
        $returnPZ->min_r_50 = $this->min_r_50;
        $returnPZ->remove_awning = self::mathDiscount($this->remove_awning, $discount);

        return $returnPZ;
    }

    static public function mathDiscount($value, $discount) : float {
        return ($value - round($value*$discount/100, 2));
    }

    public function getPriceZoneForCarOwner($id_car_owner = null){
        //можно добавить возможность менять процент в зависимости от роли, рейтинга и тд
        return $this->getWithDiscount(SettingVehicle::find()->limit(1)->one()->price_for_vehicle_procent);
    }

    static public function getNextId(){
        $id = 1;
        foreach (self::find()->all() as $item) {
            if ($item->id > $id) $id = $item->id;
        }
        return ++$id;
    }

    static public function compare(PriceZone $pricezone1, PriceZone $pricezone2) : bool{
        if($pricezone1->veh_type == $pricezone2->veh_type
            && $pricezone1->id == $pricezone2->id
            && $pricezone1->body_types == $pricezone2->body_types
            && $pricezone1->longlength == $pricezone2->longlength
            && $pricezone1->tonnage_min == $pricezone2->tonnage_min
            && $pricezone1->tonnage_max == $pricezone2->tonnage_max
            && $pricezone1->volume_min == $pricezone2->volume_min
            && $pricezone1->volume_max == $pricezone2->volume_max
            && $pricezone1->length_min == $pricezone2->length_min
            && $pricezone1->length_max == $pricezone2->length_max
            && $pricezone1->passengers == $pricezone2->passengers
            && $pricezone1->tonnage_spec_min == $pricezone2->tonnage_spec_min
            && $pricezone1->tonnage_spec_max == $pricezone2->tonnage_spec_max
            && $pricezone1->length_spec_min == $pricezone2->length_spec_min
            && $pricezone1->length_spec_max == $pricezone2->length_spec_max
            && $pricezone1->volume_spec == $pricezone2->volume_spec
            && $pricezone1->r_km == $pricezone2->r_km
            && $pricezone1->h_loading == $pricezone2->h_loading
            && $pricezone1->r_loading == $pricezone2->r_loading
            && $pricezone1->min_price == $pricezone2->min_price
            && $pricezone1->r_h == $pricezone2->r_h
            && $pricezone1->min_r_10 == $pricezone2->min_r_10
            && $pricezone1->min_r_20 == $pricezone2->min_r_20
            && $pricezone1->min_r_30 == $pricezone2->min_r_30
            && $pricezone1->min_r_40 == $pricezone2->min_r_40
            && $pricezone1->min_r_50 == $pricezone2->min_r_50
            && $pricezone1->remove_awning == $pricezone2->remove_awning
        )
            return true;
        return false;
    }

    public function hasAllBodyTypies() : bool{
        switch ($this->veh_type){
            case Vehicle::TYPE_TRUCK:
                if($this->hasBodyType(Vehicle::BODY_truck_minivan)
                    && $this->hasBodyType(Vehicle::BODY_commercial_minibus)
                    && $this->hasBodyType(Vehicle::BODY_cargo_and_passenger_minibus)
                    && $this->hasBodyType(Vehicle::BODY_awning_rear_side)
                    && $this->hasBodyType(Vehicle::BODY_rear_gate_awning)
                    && $this->hasBodyType(Vehicle::BODY_side_open)
                    && $this->hasBodyType(Vehicle::BODY_ref)
                    && $this->hasBodyType(Vehicle::BODY_van)
                    && $this->hasBodyType(Vehicle::BODY_heated_van)
                ) return true;
                break;
            case Vehicle::TYPE_PASSENGER:
                if($this->hasBodyType(Vehicle::BODY_sedan)
                    && $this->hasBodyType(Vehicle::BODY_hatchback)
                    && $this->hasBodyType(Vehicle::BODY_pass_minibus)
                    && $this->hasBodyType(Vehicle::BODY_minivan)
                    && $this->hasBodyType(Vehicle::BODY_bus)
                )  return true;
                break;
        }
        return false;
    }

    public function getCosts_for(){
        $User = Yii::$app->user;
        $return = [
            'r_km' => 0,
            'r_h' => 0,
            'r_loading' => 0,
            'min_price' => 0,
            'remove_awning' => 0,
        ];
        if($User->can('client')
            || $User->can('user')
            || $User->isGuest
        ) {
            $return = [
                'r_km' => $this->r_km,
                'r_h' => $this->r_h,
                'r_loading' => $this->r_loading,
                'min_price' => $this->min_price,
                'remove_awning' => $this->remove_awning,
            ];
        } else {
            $price_car_owner = $this->getPriceZoneForCarOwner();
            $return = [
                'r_km' => $price_car_owner->r_km . '(' . $this->r_km . ')',
                'r_h' => $price_car_owner->r_h . '(' . $this->r_h . ')',
                'r_loading' => $price_car_owner->r_loading . '(' . $this->r_loading . ')',
                'min_price' => $price_car_owner->min_price . '(' . $this->min_price . ')',
                'remove_awning' => $price_car_owner->remove_awning . '(' . $this->remove_awning . ')',
            ];
        }

        return $return;
    }

}
