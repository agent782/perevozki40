<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%routes}}".
 *
 * @property integer $id
 * @property integer $id_order
 * @property string $routeStart
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
 *
 * @property Order[] $orders
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
            [['routeStart', 'route1', 'route2', 'route3', 'route4', 'route5', 'route6', 'route7', 'route8', 'routeFinish'], 'string', 'max' => 255],
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


}
