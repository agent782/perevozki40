<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "driver_licenses".
 *
 * @property integer $id
 * @property integer $date
 * @property integer $date_end
 * @property integer $place
 * @property string $categories
 * @property string $country
 * @property integer $checked
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
            [['id', 'date', 'date_end', 'place', 'categories', 'country'], 'required'],
            [['id', 'date', 'date_end', 'place', 'checked'], 'integer'],
            [['categories'], 'string'],
            [['country'], 'string', 'max' => 64],
            [['id'], 'unique'],
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
            'date_end' => Yii::t('app', 'Date End'),
            'place' => Yii::t('app', 'Place'),
            'categories' => Yii::t('app', 'Categories'),
            'country' => Yii::t('app', 'Country'),
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
}
