<?php

namespace app\models;

use Yii;
use app\components\DateBehaviors;

/**
 * This is the model class for table "passports".
 *
 * @property integer $id
 * @property string $number
 * @property integer $date
 * @property string $place
 * @property string $country
 * @property integer $checked
 * @property string $fullInfo
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
            [['checked'], 'integer'],
            [['place', 'number'], 'string', 'max' => 255],
            [['country'], 'string', 'max' => 64],
            [['id'], 'safe'],
            ['date' , 'date', 'format' => 'php:d.m.Y'],
            ['country', 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'number' => Yii::t('app', 'Серия и номер паспорта'),
            'date' => Yii::t('app', 'Дата выдачи'),
            'place' => Yii::t('app', 'Кем выдан'),
            'country' => Yii::t('app', 'Гражданство'),
            'checked' => Yii::t('app', 'Проверен'),
        ];
    }

    public function behaviors()
    {
        return [
            'encryption' => [
                'class' => '\nickcv\encrypter\behaviors\EncryptionBehavior',
                'attributes' => [
                    'number',
//                    'date',
                    'place',
                    'country'
                ],
            ],
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

    public function getFullInfo(){
        return 'Паспорт '.$this->number . ' выдан ' . $this->place . ' ' . $this->date . '. ';
    }


}
