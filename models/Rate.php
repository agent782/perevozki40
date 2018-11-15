<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%rates}}".
 *
 * @property integer $id
 * @property string $date_change
 *
 * @property XorderXrate[] $xorderXrates
 * @property Order[] $idOrders
 * @property XvehicleXrate[] $xvehicleXrates
 * @property Vehicles[] $idVehicles
 */
class Rate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%rates}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date_change'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'date_change' => Yii::t('app', 'Date Change'),
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdOrders()
    {
        return $this->hasMany(Order::className(), ['id' => 'id_order'])->viaTable('{{%XorderXrate}}', ['id_rate' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdVehicles()
    {
        return $this->hasMany(Vehicles::className(), ['id' => 'id_vehicle'])->viaTable('{{%XvehicleXrate}}', ['id_rate' => 'id']);
    }
}
