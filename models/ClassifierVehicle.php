<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%classifier_vehicle}}".
 *
 * @property integer $id
 * @property integer $id_price_zone
 * @property integer $id_vehicle_type
 * @property integer $id_body_type
 * @property integer $long_length
 * @property integer $tMax
 * @property integer $tMin
 * @property double $vMax
 * @property double $vMin
 * @property double $lMax
 * @property double $lMin
 *
 * @property XvehicleXclassifierVehicle[] $xvehicleXclassifierVehicles
 * @property Vehicles[] $idVehicles
 * @property VehicleType $idVehicleType
 * @property BodyType $idBodyType
 * @property PriceZone $idPriceZone
 */
class ClassifierVehicle extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%classifier_vehicle}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_price_zone', 'id_vehicle_type', 'id_body_type', 'tMax'], 'required'],
            [['id_price_zone', 'id_vehicle_type', 'id_body_type', 'long_length', 'tMax', 'tMin'], 'integer'],
            [['vMax', 'vMin', 'lMax', 'lMin'], 'number'],
            [['id_vehicle_type'], 'exist', 'skipOnError' => true, 'targetClass' => VehicleType::className(), 'targetAttribute' => ['id_vehicle_type' => 'id']],
            [['id_body_type'], 'exist', 'skipOnError' => true, 'targetClass' => BodyType::className(), 'targetAttribute' => ['id_body_type' => 'id']],
            [['id_price_zone'], 'exist', 'skipOnError' => true, 'targetClass' => PriceZone::className(), 'targetAttribute' => ['id_price_zone' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'id_price_zone' => Yii::t('app', 'Id Price Zone'),
            'id_vehicle_type' => Yii::t('app', 'Id Vehicle Type'),
            'id_body_type' => Yii::t('app', 'Id Body Type'),
            'long_length' => Yii::t('app', 'Long Length'),
            'tMax' => Yii::t('app', 'T Max'),
            'tMin' => Yii::t('app', 'T Min'),
            'vMax' => Yii::t('app', 'V Max'),
            'vMin' => Yii::t('app', 'V Min'),
            'lMax' => Yii::t('app', 'L Max'),
            'lMin' => Yii::t('app', 'L Min'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
//    public function getXvehicleXclassifierVehicles()
//    {
//        return $this->hasMany(XvehicleXclassifierVehicle::className(), ['id_classifier_vehicle' => 'id']);
//    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdVehicles()
    {
        return $this->hasMany(Vehicle::className(), ['id' => 'id_vehicle'])
            ->viaTable('{{%XvehicleXclassifier_vehicle}}', ['id_classifier_vehicle' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdVehicleType()
    {
        return $this->hasOne(VehicleType::className(), ['id' => 'id_vehicle_type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdBodyType()
    {
        return $this->hasOne(BodyType::className(), ['id' => 'id_body_type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdPriceZone()
    {
        return $this->hasOne(PriceZone::className(), ['id' => 'id_price_zone']);
    }
}
