<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "price_zone".
 *
 * @property integer $id
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
 *
 * @property ClassifierVehicle[] $classifierVehicles
 */
class PriceZone extends \yii\db\ActiveRecord
{
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
            [['r_km', 'h_loading', 'min_price', 'r_h', 'min_r_10', 'min_r_20', 'min_r_30', 'min_r_40', 'min_r_50'], 'number'],
            [['r_loading'], 'integer'],
            [['min_price', 'r_h', 'min_r_10', 'min_r_20', 'min_r_30', 'min_r_40', 'min_r_50'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'r_km' => Yii::t('app', 'р/км'),
            'h_loading' => Yii::t('app', 'часов на погрузку'),
            'r_loading' => Yii::t('app', 'р/ч за переросход погрузки/разгрузки'),
            'min_price' => Yii::t('app', 'Минимальныя оплата пробег >50'),
            'r_h' => Yii::t('app', 'р/ч при почасовой оплате'),
            'min_r_10' => Yii::t('app', 'мин оплата пробег <10км'),
            'min_r_20' => Yii::t('app', 'мин оплата пробег <20км'),
            'min_r_30' => Yii::t('app', 'мин оплата пробег <30км'),
            'min_r_40' => Yii::t('app', 'мин оплата пробег <40км'),
            'min_r_50' => Yii::t('app', 'мин оплата пробег <50км'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClassifierVehicles()
    {
        return $this->hasMany(ClassifierVehicle::className(), ['id_price_zone' => 'id']);
    }
//НАДО ПРОВЕРИТЬ!!!!!
    public function getVehicles()
    {
        $Vehicles = array();
        $classVehs = $this->getClassifierVehicles()->all();
        foreach ($classVehs as $classVeh){
                $vehicles = $classVeh->getIdVehicles()->all();
                foreach ($vehicles as $vehicle){
                    array_push($Vehicles, $vehicle->attributes);
                }
        }
        return $Vehicles;

    }

public function getV($v)
{
    $V = $v;
    return $V;

}
}
