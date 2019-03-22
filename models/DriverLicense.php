<?php

namespace app\models;

use Yii;
use app\components\DateBehaviors;
use nickcv\encrypter;
use app\models\Profile;

/**
 * This is the model class for table "driver_licenses".
 *
 * @property integer $id
 * @property string $number
 * @property integer $date
 * @property string $place
 * @property integer $checked
 * @property string $photo
 * @property string $fullInfo
 *
 * @property Profile[] $profiles
 */
class DriverLicense extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'driver_licenses';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date', 'number', 'place'], 'required'],
            [['id'], 'safe'],
            ['checked', 'safe'],
            ['date', 'date', 'format' => 'php:d.m.Y'],
            [['place', 'number'], 'string', 'max' => 255],
        ];
    }

    public function behaviors()
    {
        return [
            'encryption' => [
                'class' => '\nickcv\encrypter\behaviors\EncryptionBehavior',
                'attributes' => [
                    'number'
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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'number' => Yii::t('app', 'Серия и номер водительского удостоверения'),
            'date' => Yii::t('app', 'Date'),
            'place' => Yii::t('app', 'Place'),
            'checked' => Yii::t('app', 'Checked'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfiles()
    {
        return $this->hasMany(Profile::className(), ['id_driver_license' => 'id']);
    }

    public function getFullInfo(){
        return $this->number . ' выдано ' . $this->place . ' ' . $this->date  . '. ';
    }
}
