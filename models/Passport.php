<?php

namespace app\models;

use Yii;
use app\components\DateBehaviors;

/**
 * This is the model class for table "passports".
 *
 * @property integer $id
 * @property integer $date
 * @property string $place
 * @property string $country
 * @property integer $checked
 *
 * @property Profile[] $profiles
 */
class Passport extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'passports';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'date', 'place', 'country'], 'required'],
            ['checked', 'integer'],
            [['place'], 'string', 'max' => 255],
            [['country'], 'string', 'max' => 64],
            [['id'], 'unique'],
            ['date' , 'date', 'format' => 'php:d.m.Y']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'date' => Yii::t('app', 'Date'),
            'place' => Yii::t('app', 'Place'),
            'country' => Yii::t('app', 'Country'),
            'checked' => Yii::t('app', 'Checked'),
        ];
    }

    public function behaviors()
    {
        return [
//            'encryption' => [
//                'class' => '\nickcv\encrypter\behaviors\EncryptionBehavior',
//                'attributes' => [
//                    'id',
//                    'place',
//                ],
//            ],
            'convertDate' => [
                'class' => 'app\components\DateBehaviors',
                'dateAttributes' => ['date'],
                'format' => DateBehaviors::FORMAT_DATE,
            ]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['id_passport' => 'id']);
    }


}
