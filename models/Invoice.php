<?php

namespace app\models;

use app\components\DateBehaviors;
use Yii;

/**
 * This is the model class for table "invoice".
 *
 * @property int $id
 * @property int $number
 * @property int $date
 * @property int $type
 * @property int $id_order
 * @property int $status
 * @property string $url
 * @property string $url_confirm
 * @property int $create_at
 * @property int $update_at
 */
class Invoice extends \yii\db\ActiveRecord
{
    const TYPE_INVOICE = 1;
    const TYPE_CERTIFICATE = 2;

    const STATUS_NEW = 1;


    public $upload_file;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoice';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['number','date'], 'required'],
            ['upload_file', 'required', 'skipOnEmpty' => true, 'message' => 'Файл не выбран'],
            [[ 'type', 'status', 'id_order'], 'safe'],
            [['number', 'type', 'status'], 'integer'],
            [['url', 'url_confirm'], 'string'],
            ['upload_file', 'file', 'maxSize' => 20000000],
            ['upload_file', 'file', 'extensions' => ['pdf', 'jpg']],
            ['date', 'date', 'format' => 'php:d.m.Y'],
            ['status', 'default', 'value' => self::STATUS_NEW],
            [['create_at', 'update_at'], 'default', 'value' => date('d.m.Y H:i')],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'dateConvert' => [
                'class' => DateBehaviors::class,
                'dateAttributes' => ['date'],
                'format' => DateBehaviors::FORMAT_DATE
            ],
            'datetimeConvert' => [
                'class' => DateBehaviors::class,
                'dateAttributes' => ['create_at', 'update_at'],
                'format' => DateBehaviors::FORMAT_DATETIME
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => 'Номер',
            'date' => 'Дата',
            'type' => 'Type',
            'status' => 'Status',
            'url' => 'Url',
            'url_confirm' => 'Url Confirm',
            'create_at' => 'Create At',
            'update_at' => 'Update At',
        ];
    }
}
