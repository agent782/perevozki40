<?php

namespace app\models\setting;

use Yii;

/**
 * This is the model class for table "setting_finance".
 *
 * @property int $id
 * @property int $id_default_company
 */
class SettingFinance extends Setting
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'setting_finance';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_default_company'], 'required'],
            [['id_default_company'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_default_company' => 'Id Default Company',
        ];
    }

    static public function getIdDefaultCompany(){
        return self::findOne()->id_our_company;
    }
}
