<?php

namespace app\models;

use Yii;
use deka6pb\geocoder\Geocoder;


/**
 * This is the model class for table "{{%routes}}".
 *
 * @property integer $id
 * @property integer $id_order
 * @property string $routeStart
 * @property string $startCity
 * @property string $finishCity
 * @property string $route1
 * @property string $route2
 * @property string $route3
 * @property string $route4
 * @property string $route5
 * @property string $route6
 * @property string $route7
 * @property string $route8
 * @property string $routeFinish
 * @property integer $distance
 * @property Order[] $orders
 * @property string fullRoute
 */
class Route extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%routes}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['routeStart', 'routeFinish'], 'required'],
            [['distance'], 'integer'],
            [['routeStart', 'route1', 'route2', 'route3', 'route4', 'finishCity',
                'route5', 'route6', 'route7', 'route8', 'routeFinish', 'startCity'], 'string', 'max' => 2254],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'routeStart' => Yii::t('app', 'Начало маршрута'),
            'route1' => Yii::t('app', 'Route1'),
            'route2' => Yii::t('app', 'Route2'),
            'route3' => Yii::t('app', 'Route3'),
            'route4' => Yii::t('app', 'Route4'),
            'route5' => Yii::t('app', 'Route5'),
            'route6' => Yii::t('app', 'Route6'),
            'route7' => Yii::t('app', 'Route7'),
            'route8' => Yii::t('app', 'Route8'),
            'routeFinish' => Yii::t('app', 'Конец маршрута'),
            'distance' => Yii::t('app', 'Distance'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['id_route' => 'id']);
    }

    public function beforeSave($insert)
    {
        $coder = Geocoder::build(Geocoder::TYPE_YANDEX);
        $obj_start = $coder::findOneByAddress($this->routeStart);
        $obj_finish = $coder::findOneByAddress($this->routeFinish);

        if($obj_start){
            if($obj_start->data){
                $this->startCity = $obj_start->data['city'];
            }else {
                $this->startCity = $this->routeStart;
            }
        } else {
            $this->startCity = $this->routeStart;
        }

        if($obj_finish){
            if($obj_finish->data){
                $this->finishCity = $obj_finish->data['city'];
            }else {
                $this->finishCity = $this->routeFinish;
            }
        } else {
            $this->finishCity = $this->routeFinish;
        }

        self::optimizationPoints();

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function getFullRoute(){
        $return = $this->routeStart . '<br>';
        for($i = 1; $i<9; $i++){
            $attribute = 'route' . $i;
            if($this->$attribute) {
                $return .=  '-  ' .  $this->$attribute . ' <br>';
            }
        }
        $return .= '-  ' . $this->routeFinish;
        $return .= '<br>Приблизительный пробег: &asymp;' . $this->distance . 'km <br>';

        return $return;

    }

    public function optimizationPoints(){
        $points = [];
        for($i=1; $i<9; $i++){
            $attribute = 'route' . $i;
            if($this->$attribute){
                $points[] = $this->$attribute;
                $this->$attribute = null;
            }
        }
        if($points) {
            for ($i = 0; $i < count($points); $i++) {
                $attribute = 'route' . ($i+1);
                $this->$attribute = $points[$i];
            }
        }
    }

}
