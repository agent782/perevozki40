<?php

namespace app\models\settings;

use Yii;

/**
 * This is the model class for table "setting".
 *
 * @property int $sms_code_update_phone
 * @property int $sms_code_reset_password
 */
class SettingSMS extends \app\models\setting\Setting
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'setting';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sms_code_update_phone', 'sms_code_reset_password'], 'integer']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'sms_code_update_phone' => 'Sms Code Update Phone',
        ];
    }
}
