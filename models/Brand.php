<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "brands".
 *
 * @property integer $id
 * @property string $brand
 */
class Brand extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'brands';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['brand'], 'required'],
            [['brand'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'brand' => 'Марка',
        ];
    }
}
