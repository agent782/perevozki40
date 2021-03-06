<?php

namespace app\models\settings;

use Yii;

/**
 * This is the model class for table "setting_profile".
 *
 * @property int $id_user
 * @property int $send_email
 * @property int $send_sms
 * @property int $send_push
 * @property int $mailing
 */
class SettingProfile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'setting_profile';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_user'], 'unique'],
            [['send_email', 'send_sms', 'send_push', 'mailing'], 'default', 'value' => true],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_user' => 'Id User',
            'send_email' => 'Send Email',
            'send_sms' => 'Send Sms',
            'send_push' => 'Send Push',
        ];
    }
}
