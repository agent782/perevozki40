<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "orders".
 *
 * @property integer $id
 * @property integer $id_user
 * @property string $date_create
 * @property string $datetime_start
 * @property string $datetime_start_max
 * @property string $date_start_max
 * @property string $time_start_max
 * @property integer $id_vehicle
 * @property integer $id_route
 * @property integer $id_route_real
 * @property integer $weight
 * @property integer $long
 * @property integer $width
 * @property integer $height
 * @property double $volume
 * @property integer $ep
 * @property integer $rp
 * @property integer $lp
 * @property integer $id_vehicle_type
 * @property string $cargo
 * @property resource $id_tariff
 * @property integer $longlenth
 *
 * @property XorderXloadingtype[] $xorderXloadingtypes
 * @property LoadingType[] $idLoadingTypes
 * @property XorderXtypebody[] $xorderXtypebodies
 * @property BodyType[] $idBodytypes
 * @property Routes $idRoute
 * @property Routes $idRouteReal
 * @property User $idUser
 * @property VehicleType $idVehicleType
 */
class OrderOLD extends \yii\db\ActiveRecord
{
    public $id_loadingTypes = array();
    public $id_bodyTypes = array();
    public $id_rates = array();
    public $type_pallet = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_user', ], 'required'],
            [['id_user', 'id_vehicle', 'id_route', 'id_route_real', 'ep', 'rp', 'lp', 'id_vehicle_type', 'longlenth'], 'integer'],
            [['weight', 'longer', 'width', 'height'], 'number'],
            [['id_rates', 'id_loadingTypes', 'id_bodyTypes', 'date_create', 'datetime_start', 'datetime_start_max', 'date_start_max', 'time_start_max'], 'safe'],
            [['volume'], 'number'],
            [['id_tariff'], 'string'],
            [['cargo'], 'string', 'max' => 255],
            [['id_route'], 'exist', 'skipOnError' => true, 'targetClass' => Route::className(), 'targetAttribute' => ['id_route' => 'id']],
            [['id_route_real'], 'exist', 'skipOnError' => true, 'targetClass' => Route::className(), 'targetAttribute' => ['id_route_real' => 'id']],
            [['id_user'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id_user' => 'id']],
            [['id_vehicle_type'], 'exist', 'skipOnError' => true, 'targetClass' => VehicleType::className(), 'targetAttribute' => ['id_vehicle_type' => 'id']],
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
            'datetime_start' => Yii::t('app', 'Datetime Start'),
            'datetime_start_max' => Yii::t('app', 'Datetime Start Max'),
            'date_start_max' => Yii::t('app', 'Date Start Max'),
            'time_start_max' => Yii::t('app', 'Time Start Max'),
            'id_vehicle' => Yii::t('app', 'Id Vehicle'),
            'id_route' => Yii::t('app', 'Id Route'),
            'id_route_real' => Yii::t('app', 'Id Route Real'),
            'weight' => Yii::t('app', 'Вес'),
            'longer' => Yii::t('app', 'Длинна'),
            'width' => Yii::t('app', 'Ширина'),
            'height' => Yii::t('app', 'Высота'),
            'volume' => Yii::t('app', 'Объем'),
            'ep' => Yii::t('app', 'Паллет 1,2*0,8'),
            'rp' => Yii::t('app', 'Паллет 1,2*1'),
            'lp' => Yii::t('app', 'Паллет 1,2*1,2'),
            'id_vehicle_type' => Yii::t('app', 'Id Vehicle Type'),
            'cargo' => Yii::t('app', 'Cargo'),
            'id_tariff' => Yii::t('app', 'Id Tariff'),
            'longlenth' => Yii::t('app', 'Longlenth'),
        ];
    }

    public function getVehicleType()
    {
        return $this->hasOne(VehicleType::className(), ['id_vehicle_type' => 'id']);
    }
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoute()
    {
        return $this->hasOne(Route::className(), ['id' => 'id_route']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRouteReal()
    {
        return $this->hasOne(Route::className(), ['id' => 'id_route_real']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdUser()
    {
        return $this->hasOne(User::className(), ['id' => 'id_user']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */

    public function getIdRates()
    {
        return $this->hasMany(Order::className(), ['id' => 'id_rate'])->viaTable('{{%XorderXrate}}', ['id_order' => 'id']);
    }

}
