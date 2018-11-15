<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ur".
 *
 * @property integer $id
 * @property integer $inn
 * @property string $address
 * @property string $name_short
 */
class Ur extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ur';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['inn', 'address', 'name_short'], 'required'],
            [['inn'], 'integer'],
            [['address', 'name_short'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'inn' => Yii::t('app', 'Inn'),
            'address' => Yii::t('app', 'Address'),
            'name_short' => Yii::t('app', 'Name Short'),
        ];
    }
}
