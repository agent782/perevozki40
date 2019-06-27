<?php

namespace app\models;

use Yii;
use yii\bootstrap\Html;

/**
 * This is the model class for table "{{%body_type}}".
 *
 * @property integer $id
 * @property string $body
 * @property string $body_short
 * @property string $bodyShortWithTip
 * @property string $image
 * @property integer $id_type_vehicle
 *
 * @property XorderXtypebody[] $xorderXtypebodies
 */
class BodyType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%body_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['body', 'image', 'id_type_vehicle'], 'required'],
            [['id_type_vehicle'], 'integer'],
            [['body', 'image'], 'string', 'max' => 255],
            [['body_short', 'image'], 'string', 'max' => 16],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'body' => Yii::t('app', 'Body'),
            'image' => Yii::t('app', 'Image'),
            'id_type_vehicle' => Yii::t('app', 'Id Type Vehicle'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasMany(OrderOLD::className(), ['id' => 'id_order'])
            -> viaTable('XorderXtypebody', ['id_bodytype' => 'id']);
    }
    public function getVehicle()
    {
        return $this->hasMany(Vehicle::className(), ['id' => 'id_vehicle'])
            -> viaTable('XvehicleXtypebody', ['id_bodytype' => 'id']);
    }
   static public function getBodyTypies($id_vehicle_type, bool $any = false){
        $BTypies = BodyType::find()
            ->where(['id_type_vehicle' => $id_vehicle_type])
            ->orderBy(['id' => SORT_DESC])
        ;

        if($id_vehicle_type != Vehicle::TYPE_SPEC && $any){
            $BTypies = $BTypies ->orWhere(['id' => 0]);
        }

        return $BTypies->all();
    }

    public function getBodyShortWithTip(){
        return '<b style = "cursor:help;" title =' . $this->body . '>' . $this->body_short . '</b>';
    }
}
