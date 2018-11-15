<?php

namespace app\models;

use Yii;
use yii2tech\ar\linkmany\LinkManyBehavior;

/**
 * This is the model class for table "{{%orders}}".
 *
 * @property integer $id
 * @property integer $id_user
 * @property string $date_create
 * @property string $date_start
 * @property string $date_finish
 * @property integer $id_vehicle
 * @property integer $id_route
 * @property integer $weight
 * @property integer $long
 * @property integer $width
 * @property integer $height
 * @property integer $ep
 * @property integer $ap
 * @property string $typebody
 * @property string $typeload
 * @property string $cargo
 * @property resource $id_tariff
 * @property integer $longlenth
 *
 * @property XorderXtypebody[] $xorderXtypebodies
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $id_loadingTypes = array();
    public $id_bodyTypes = array();
    public $type_pallet = 0;
    public function behaviors()
    {
        return [

        ];
    }


    public static function tableName()
    {
        return '{{%orders}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_user', 'date_create', 'id_loadingTypes'], 'required'],
            [['id_user', 'id_vehicle', 'id_route', 'weight', 'long', 'width', 'height', 'ep', 'longlenth', 'id_vehicle_type'], 'integer'],
            [['date_create'], 'safe'],
            [['id_tariff'], 'string'],
            [['cargo'], 'string', 'max' => 255],

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
            'date_create' => Yii::t('app', 'Date Create'),
            'date_start' => Yii::t('app', 'Date Start'),
            'date_finish' => Yii::t('app', 'Date Finish'),
            'id_vehicle' => Yii::t('app', 'Id Vehicle'),
            'id_route' => Yii::t('app', 'Id Route'),
            'weight' => Yii::t('app', 'Weight'),
            'long' => Yii::t('app', 'Long'),
            'width' => Yii::t('app', 'Width'),
            'height' => Yii::t('app', 'Height'),
            'ep' => Yii::t('app', 'Ep'),
            'id_vehicle_type' => Yii::t('app', 'Тип автотранспорта'),
            'cargo' => Yii::t('app', 'Cargo'),
            'id_tariff' => Yii::t('app', 'Id Tariff'),
            'id_loadingTypes' => Yii::t('app', 'Погрузка / выгрузка'),
            'id_bodyTypes' => Yii::t('app', 'Тип кузова'),
            'longlenth' => Yii::t('app', 'Груз - "длинномер"'),
            'datetime_start' => Yii::t('app', 'Дата и время подачи'),
            'datetime_start_max' => Yii::t('app', 'Крайние дата и время подачи'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBodytype()
    {
        return $this->hasMany(BodyType::className(), ['id' => 'id_bodytype'])
            -> viaTable('XorderXtypebody', ['id_order' => 'id']);
    }
    public function getLoadingTypes()
    {
        return $this->hasMany(LoadingType::className(), ['id' => 'id_loading_type'])
            -> viaTable('XorderXloadingtype', ['id_order' => 'id']);
    }
    public function getVehicleType()
    {
        return $this->hasOne(VehicleType::className(), ['id_vehicle_type' => 'id']);
    }
}
