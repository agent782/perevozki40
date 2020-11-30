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
 * @property int $date
 */
class Subscribe extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_ = 1;

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
            [['id', 'email', 'status', 'date'], 'required'],
            ['status', 'integer'],
            ['email', 'email'],
            ['date', 'default', 'value' => date('d.m.Y H:i:s')],
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
