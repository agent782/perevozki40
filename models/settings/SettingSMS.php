<?php

namespace app\models\settings;

use Yii;

/**
 * This is the model class for table "setting".
 *
 * @property int $id
 * @property int $last_num_contract
 * @property string $noPhotoPath
 * @property int $FLAG_EXPIRED_ORDER
 * @property double $user_discount_cash
 * @property double $client_discount_cash
 * @property double $vip_client_discount_cash
 * @property double $user_discount_card
 * @property double $client_discount_card
 * @property double $vip_client_discount_card
 * @property double $procent_vehicle
 * @property double $procent_vip_vehicle
 * @property int $sms_code_update_phone
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
            [['id', 'noPhotoPath'], 'required'],
            [['id', 'last_num_contract', 'FLAG_EXPIRED_ORDER', 'sms_code_update_phone'], 'integer'],
            [['user_discount_cash', 'client_discount_cash', 'vip_client_discount_cash', 'user_discount_card', 'client_discount_card', 'vip_client_discount_card', 'procent_vehicle', 'procent_vip_vehicle'], 'number'],
            [['noPhotoPath'], 'string', 'max' => 255],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'last_num_contract' => 'Last Num Contract',
            'noPhotoPath' => 'No Photo Path',
            'FLAG_EXPIRED_ORDER' => 'Flag Expired Order',
            'user_discount_cash' => 'User Discount Cash',
            'client_discount_cash' => 'Client Discount Cash',
            'vip_client_discount_cash' => 'Vip Client Discount Cash',
            'user_discount_card' => 'User Discount Card',
            'client_discount_card' => 'Client Discount Card',
            'vip_client_discount_card' => 'Vip Client Discount Card',
            'procent_vehicle' => 'Procent Vehicle',
            'procent_vip_vehicle' => 'Procent Vip Vehicle',
            'sms_code_update_phone' => 'Sms Code Update Phone',
        ];
    }
}
