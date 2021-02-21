<?php

namespace app\models;

use Yii;
use app\components\DateBehaviors;

/**
 * This is the model class for table "calendar_vehicle".
 *
 * @property int $id
 * @property int $id_vehicle
 * @property int $date
 * @property int $status
 * @property string $statusText
 */
class CalendarVehicle extends \yii\db\ActiveRecord
{
    const STATUS_BUSY = 0;
    const STATUS_FREE = 1;
    const STATUS_HALF_TIME = 2;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'calendar_vehicle';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_vehicle', 'date', 'status'], 'required'],
            [['id_vehicle', 'date', 'status'], 'integer'],
//            ['date', 'date', 'format' => 'php:d.m.Y']
        ];
    }

//    public function behaviors()
//    {
//        return [
//            'convertDateTime' => [
//                'class' => DateBehaviors::class,
//                'dateAttributes' => ['date'],
//                'format' => DateBehaviors::FORMAT_DATE,
//            ],
//        ];
//    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_vehicle' => 'Id Vehicle',
            'date' => 'Date',
            'status' => 'Status',
        ];
    }

    static public function getArrayListStatuses(){
        return [
            self::STATUS_BUSY => 'Занят',
            self::STATUS_FREE => 'Свободен',
            self::STATUS_HALF_TIME => 'Частично занят'
        ];
    }

    public function getStatusText(){
        switch ($this->status){
            case self::STATUS_FREE:
                return 'Свободен';
                break;
            case self::STATUS_BUSY:
                return 'Занят';
                break;
            case self::STATUS_HALF_TIME:
                return 'Частично занят';
                break;
            default:
                return null;
                break;
        }
    }
}
