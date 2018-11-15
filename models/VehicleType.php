<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "vehicle_type".
 *
 * @property integer $id
 * @property string $type
 */
class VehicleType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vehicle_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            ['id', 'integer'],
            [['type'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Type'),
        ];
    }
    public function getVehicle()
    {
        return $this->hasMany(Vehicle::className(), ['id' => 'id_vehicle'])
            -> viaTable('XvehicleXtypevehicle', ['id_typevehicle' => 'id']);
    }
}
