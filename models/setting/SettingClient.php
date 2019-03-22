<?php

namespace app\models\setting;

use Yii;

/**
 * This is the model class for table "setting_client".
 *
 * @property int $id
 * @property double $user_discount_cash
 * @property double $client_discount_cash
 * @property double $vip_client_discount_cash
 * @property double $user_discount_card
 * @property double $client_discount_card
 * @property double $vip_client_discount_card
 */
class SettingClient extends \app\models\setting\Setting
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'setting_client';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_discount_cash', 'client_discount_cash', 'vip_client_discount_cash', 'user_discount_card', 'client_discount_card', 'vip_client_discount_card'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_discount_cash' => 'User Discount Cash',
            'client_discount_cash' => 'Client Discount Cash',
            'vip_client_discount_cash' => 'Vip Client Discount Cash',
            'user_discount_card' => 'User Discount Card',
            'client_discount_card' => 'Client Discount Card',
            'vip_client_discount_card' => 'Vip Client Discount Card',
        ];
    }

}
