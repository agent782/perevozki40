<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%loding_type}}".
 *
 * @property integer $id
 * @property string $type
 * @property string $image
 *
 * @property XvehicleXlodingtype[] $xvehicleXlodingtypes
 * @property Vehicles[] $idVehicles
 */
class LoadingType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%loading_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'image'], 'required'],
            [['type'], 'string', 'max' => 64],
            [['image'], 'string', 'max' => 255],
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
            'image' => Yii::t('app', 'Image'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVehicles()
    {
        return $this->hasMany(Vehicle::className(), ['id' => 'id_vehicle'])
            ->viaTable('{{%XvehicleXlodingtype}}', ['id_loadingtype' => 'id']);
    }
    public function getOrder()
    {
        return $this->hasMany(OrderOLD::className(), ['id' => 'id_order'])
            -> viaTable('XorderXloadingtype', ['id_loading_type' => 'id']);
    }

    static public function getLoading_typies($id_vehicle_type){
        if($id_vehicle_type == Vehicle::TYPE_TRUCK){
            return self::find()->all();
        }
        return false;

    }
}
