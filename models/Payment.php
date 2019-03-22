<?php

namespace app\models;

use Yii;
use yii\bootstrap\Html;

/**
 * This is the model class for table "payment".
 *
 * @property integer $id
 * @property double $cost
 * @property integer $type
 * @property integer $date
 * @property integer $status
 * @property string $comments
 * @property string $sys_info
 */
class Payment extends \yii\db\ActiveRecord
{

    const TYPE_SBERBANK_CARD = 2;
    const TYPE_CASH = 1;
    const TYPE_BANK_TRANSFER = 3;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cost'], 'number'],
            [['type', 'date', 'status'], 'integer'],
            [['comments', 'sys_info'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cost' => 'Cost',
            'type' => 'Type',
            'date' => 'Date',
            'status' => 'Status',
            'comments' => 'Comments',
            'sys_info' => 'Sys Info',
        ];
    }
}
