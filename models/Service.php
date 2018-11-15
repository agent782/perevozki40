<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "service".
 *
 * @property integer $id
 * @property integer $id_user
 * @property integer $id_company
 * @property integer $type
 * @property integer $create_at
 * @property integer $update_at
 * @property integer $cost
 * @property integer $id_payment
 * @property integer $status
 * @property string $comments
 */
class Service extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_user', 'id_company', 'type', 'create_at', 'update_at', 'cost', 'id_payment', 'status', 'comments'], 'required'],
            [['id_user', 'id_company', 'type', 'create_at', 'update_at', 'cost', 'id_payment', 'status'], 'integer'],
            [['comments'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_user' => 'Id User',
            'id_company' => 'Id Company',
            'type' => 'Type',
            'create_at' => 'Create At',
            'update_at' => 'Update At',
            'cost' => 'Cost',
            'id_payment' => 'Id Payment',
            'status' => 'Status',
            'comments' => 'Comments',
        ];
    }
}
