<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reg_licenses".
 *
 * @property integer $id
 * @property integer $date
 * @property integer $place
 * @property integer $country
 * @property integer $checked
 *
 * @property Profile[] $profiles
 */
class RegLicense extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reg_licenses';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'date', 'place', 'country'], 'required'],
            [['id', 'date', 'place', 'country', 'checked'], 'integer'],
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
            'place' => Yii::t('app', 'Place'),
            'country' => Yii::t('app', 'Country'),
            'checked' => Yii::t('app', 'Checked'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfiles()
    {
        return $this->hasMany(Profile::className(), ['id_reg_license' => 'id']);
    }
}
