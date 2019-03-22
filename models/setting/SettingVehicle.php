<?php

namespace app\models\setting;

use Yii;

/**
 * This is the model class for table "setting_vehicle".
 *
 * @property int $id
 * @property double $procent_vehicle
 * @property double $procent_vip_vehicle
 * @property integer $price_for_vehicle_procent
 */
class SettingVehicle extends Setting
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'setting_vehicle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['procent_vehicle', 'procent_vip_vehicle', 'price_for_vehicle_procent'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'procent_vehicle' => 'Procent Vehicle',
            'procent_vip_vehicle' => 'Procent Vip Vehicle',
        ];
    }
}
