<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "review".
 *
 * @property int $id
 * @property int $value
 * @property int $id_user_from
 * @property int $id_user_to
 * @property string $comment
 * @property int $type
 * @property int $status
 * @property int $create_at
 * @property int $update_at
 * @property int $id_message
 */
class Review extends \yii\db\ActiveRecord
{
    const TYPE_TO_VEHICLE = 1;
    const TYPE_TO_CLIENT = 2;

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_ONCHECKING = 2;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'review';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['value', 'id_user_from', 'id_user_to', 'comment', 'type'], 'required'],
            [['value', 'id_user_from', 'id_user_to', 'type', 'status', 'id_message'], 'integer'],
            [['comment'], 'string'],
            [['create_at', 'update_at'], 'default', 'value' => date('d.m.Y H:i')],
            ['status', 'default', 'value' => self::STATUS_ONCHECKING],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'value' => 'Value',
            'id_user_from' => 'Id User From',
            'id_user_to' => 'Id User To',
            'comment' => 'Comment',
            'type' => 'Type',
            'status' => 'Status',
            'create_at' => 'Create At',
            'update_at' => 'Update At',
        ];
    }
}
