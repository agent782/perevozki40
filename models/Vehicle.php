<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "vehicles".
 *
 * @property integer $id
 * @property integer $id_user
 * @property integer $id_type_vehicle
 * @property integer $id_type_body_1
 * @property integer $id_type_body_2
 * @property integer $id_type_body_3
 * @property integer $tonnage
 * @property integer $length
 * @property integer $width
 * @property integer $height
 * @property integer $passengers
 * @property integer $id_loding_type
 * @property integer $id_loding_type_2
 * @property integer $id_loding_type_3
 * @property integer $ep
 */
class Vehicle extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vehicles';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_user', 'id_type_vehicle', 'id_type_body_1', 'tonnage', 'length', 'width', 'height', 'passengers', 'id_loding_type', 'ep'], 'required'],
            [['id_user', 'id_type_vehicle', 'id_type_body_1', 'id_type_body_2', 'id_type_body_3', 'tonnage', 'length', 'width', 'height', 'passengers', 'id_loding_type', 'id_loding_type_2', 'id_loding_type_3', 'ep'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'id_user' => Yii::t('app', 'Id User'),
            'id_type_vehicle' => Yii::t('app', 'Id Type Vehicle'),
            'id_type_body_1' => Yii::t('app', 'Id Type Body 1'),
            'id_type_body_2' => Yii::t('app', 'Id Type Body 2'),
            'id_type_body_3' => Yii::t('app', 'Id Type Body 3'),
            'tonnage' => Yii::t('app', 'Tonnage'),
            'length' => Yii::t('app', 'Length'),
            'width' => Yii::t('app', 'Width'),
            'height' => Yii::t('app', 'Height'),
            'passengers' => Yii::t('app', 'Passengers'),
            'id_loding_type' => Yii::t('app', 'Id Loding Type'),
            'id_loding_type_2' => Yii::t('app', 'Id Loding Type 2'),
            'id_loding_type_3' => Yii::t('app', 'Id Loding Type 3'),
            'ep' => Yii::t('app', 'Ep'),
        ];
    }
    public function getBodytype()
    {
        return $this->hasMany(BodyType::className(), ['id' => 'id_bodytype'])
            -> viaTable('XvehicleXtypebody', ['id_vehicle' => 'id']);
    }
    public function getVehicletype()
    {
        return $this->hasMany(VehicleType::className(), ['id' => 'id_typevehicle'])
            -> viaTable('XvehicleXtypevehicle', ['id_vehicle' => 'id']);
    }
    public function getLoadingtype()
    {
        return $this->hasMany(LoadingType::className(), ['id' => 'id_loadingtype'])
            ->viaTable('{{%XvehicleXlodingtype}}', ['id_vehicle' => 'id']);
    }
    public function getClassifierVehicles()
    {
        return $this->hasMany(ClassifierVehicle::className(), ['id' => 'id_classifier_vehicle'])->viaTable('{{%XvehicleXclassifier_vehicle}}', ['id_vehicle' => 'id']);
    }
    public function getIdRates()
    {
        return $this->hasMany(Vehicles::className(), ['id' => 'id_rate'])->viaTable('{{%XvehicleXrate}}', ['id_vehicle' => 'id']);
    }
}
