<?php

namespace app\models;

use Yii;
use app\components\DateBehaviors;

/**
 * This is the model class for table "subscribe".
 *
 * @property int $id
 * @property string $email
 * @property int $status
 * @property int $type
 * @property int $date
 */
class Subscribe extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_NO_ACTIVE = 2;

    const TYPE_AUTO = 1;
    const TYPE_MANUAL = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subscribe';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            ['status', 'integer'],
            ['email', 'email'],
            ['email', 'unique', 'message' => 'Вы уже подписаны на нашу рассылку!'],
            ['date', 'default', 'value' => date('d.m.Y H:i:s')],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['type', 'default', 'value' => self::TYPE_AUTO]
        ];
    }

    public function behaviors()
    {
        return [
            'convertDateTime' => [
                'class' => 'app\components\DateBehaviors',
                'dateAttributes' => ['date'],
                'format' => DateBehaviors::FORMAT_DATETIME,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'status' => 'Status',
            'date' => 'Date',
        ];
    }

}
