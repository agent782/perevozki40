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

    const SCENARIO_TRUCK = Vehicle::TYPE_TRUCK;
    const SCENARIO_PASS = Vehicle::TYPE_PASSENGER;
    const SCENARIO_SPEC = Vehicle::TYPE_SPEC;

    const SCENARIO_MANIPULATOR = Vehicle::BODY_manipulator;
    const SCENARIO_CRANE = Vehicle::BODY_crane;
    const SCENARIO_EXCAVATOR = Vehicle::BODY_excavator;
    const SCENARIO_DUMP = Vehicle::BODY_dump;
    const SCENARIO_EXCAVATOR_LOADER = Vehicle::BODY_excavator_loader;

    const STATUS_NEW = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_OLD = 2;

    const SORT_TRUCK = [
        'enableMultiSort' => true,
        'attributes' => [
            'longLength',
            'tonnage_max',
            'length_max',
            'volume_max',
            'length_long_max',
            'tonnage_long_max'
        ],
        'defaultOrder' => [
            'longLength' => SORT_ASC,
            'tonnage_max' => SORT_ASC,
            'length_max' => SORT_ASC,
            'volume_max' => SORT_ASC,
            'length_long_max' => SORT_ASC,
            'tonnage_long_max' => SORT_ASC
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
            'bodiesColumn' => [
                'asc' => [
                    'bodiesColumn' => 'asc'
                ],
                'default' => SORT_ASC
            ]
        ],
        'defaultOrder' => [
//            'bodiesColumn' => SORT_ASC,
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
            [['id', 'unique_index', 'r_km'], 'required'],
            [['veh_type', 'body_types'], 'required', 'message' => 'Выберите хотя бы один из вариантов'],
//            [['veh_type', 'passengers'], 'integer'],
//            [[], 'validateTRUCK', 'skipOnEmpty' => false],
//            ['volume_spec', 'required'],
            [['tonnage_min', 'tonnage_max', 'volume_min', 'volume_max', 'length_min', 'length_max',
                'tonnage_long_min', 'tonnage_long_max', 'length_long_min', 'length_long_max',
                'tonnage_spec_min', 'tonnage_spec_max', 'length_spec_min', 'length_spec_max', 'volume_spec', 'r_km', 'r_loading',
                'h_loading', 'min_price', 'r_h', 'min_r_10', 'min_r_20', 'min_r_30', 'min_r_40', 'min_r_50', 'remove_awning'], 'number'],
            [[ 'length_long_min', 'tonnage_long_max', 'tonnage_long_min', 'length_long_max'], 'safe'],

            [[
                'tonnage_min', 'tonnage_max', 'volume_min', 'volume_max', 'length_min', 'length_max', 'tonnage_long_min', 'tonnage_long_max',
                'length_long_min', 'length_long_max', 'tonnage_spec_min', 'tonnage_spec_max', 'length_spec_min', 'length_spec_max', 'volume_spec',
            ], 'default', 'value' => null],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            [['created_at', 'updated_at'], 'default','value' => date('d.m.Y H:m')],
            [['bodiesColumn','longlength','remove_awning', 'created_at', 'updated_at', 'history'], 'safe'],

            [['passengers'], 'validatePASS', 'enableClientValidation' => true, 'skipOnEmpty' => false],

            [['tonnage_min', 'tonnage_max', 'length_min', 'length_max'], 'validateTRUCK', 'skipOnEmpty' => false],
        ];
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
            'tonnage_min' => 'Грузоподъемность минимальная',
            'tonnage_max' => 'Грузоподъемность максимальная',
            'volume_min' => 'Объем минимальный',
            'volume_max' => 'Объем максимальный',
            'length_min' => 'Длина кузова минимальная',
            'length_max' => 'Длина кузова максимальная',
            'tonnage_long_min' => 'Грузоподъемность при перевозке длинномера минимальная',
            'tonnage_long_max' => 'Грузоподъемность при перевозке длинномера максимальная',
            'length_long_min' => 'Длина длинномера минимальная',
            'length_long_max' => 'Длина длинномера максимальная',
            'passengers' => 'Количество пассажиров',
            'tonnage_spec_min' => 'Грузоподъемность механизма (стрелы) минимальная',
            'tonnage_spec_max' => 'Грузоподъемность механизма (стрелы) максимальная',
            'length_spec_min' => 'Длина механизма (стрелы) минимальная',
            'length_spec_max' => 'Длина механизма (стрелы) максимальная',
            'volume_spec' => 'Объем механизма (ковша)',
            'r_km' => 'Руб/км',
            'h_loading' => 'Время на погрузку/разгрузку',
            'r_loading' => 'Переработка, руб/час',
            'min_price' => 'Минимальная оплата при пробеге >100км, руб.',
            'r_h' => 'Руб/час',
            'min_r_10' => 'Минимальная оплата при пробеге <20км, ч.',
            'min_r_20' => 'Минимальная оплата при пробеге >20км, ч.',
            'min_r_30' => 'Минимальная оплата при пробеге >40км, ч.',
            'min_r_40' => 'Минимальная оплата при пробеге >60км, ч.',
            'min_r_50' => 'Минимальная оплата при пробеге >80км, ч.',
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

    public function validateLonglength($attribute){
        if($this->longlength && !$this->$attribute){
            $this->addError($attribute, 'Необходимо заполнить.');
        }
    }
    public function validateNotLonglength($attribute){
        if($this->longlength && !$this->$attribute && $this->veh_type==Vehicle::TYPE_TRUCK){
            $this->addError($attribute, 'Необходимо заполнить.');
        }
    }
    public function validateTRUCK($attribute){
        if(!$this->$attribute && $this->veh_type == Vehicle::TYPE_TRUCK){
            $this->addError($attribute, 'Необходимо заполнить.');
        }
    }

    public function validatePASS($attribute){
        if(!$this->$attribute && $this->veh_type == Vehicle::TYPE_PASSENGER){
            $this->addError($attribute, 'Необходимо заполнить.');

        }
    }

    public function getBodyTypies(){
        if($this->body_types){
            return BodyType::find()->where(['in', 'id', $this->body_types])->all();
        } return false;
    }

    public function validateSPEC($attribute){

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


        switch ($this->veh_type){
            case Vehicle::TYPE_TRUCK:
                if($this->longlength){
                    $result .=
                        '<p>Груз-длинномер (выход за габариты кузова по длинне до 2-х метров со знаком). </p>'
                        ;

                }
                $result .=
                    '<p>' . $this->tonnage_min . ' - ' . $this->tonnage_max . 'т. - Грузоподъемность </p>'
                    . '<p>' . $this->length_min . ' - ' . $this->length_max . 'м. - Длина кузова </p>'
                    . '<p>' . $this->volume_min . ' - ' . $this->volume_max . 'м3. - объем кузова </p>'
                    ;
                break;

            case Vehicle::TYPE_PASSENGER:
//                $result .=
                break;

            case Vehicle::TYPE_SPEC:
//                $result .=
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
                    $cost = $this->min_r_10 * $this->r_h;
                }
                else if ($distance >= 40 && $distance<60){
                    $cost = $this->min_r_10 * $this->r_h;
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
        if(!$cost) return 'Невозможно расчитать стоимость. Неверно задан маршрут!';
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
        return ($value - round($value*$discount/100, 1));
    }


    public function getPriceZoneForCarOwner($id_car_owner){
        //можно добавить возможность менять процент в зависимости от роли, рейтинга и тд
        return $this->getWithDiscount(SettingVehicle::find()->limit(1)->one()->price_for_vehicle_procent);
    }
}
