<?php

namespace app\models\setting;

use Yii;

/**
 * This is the model class for table "setting_finance".
 *
 * @property int $id
 * @property int $id_default_company
 * @property bool $invoices_to_client_email
 * @property bool $invoices_to_company_email
 */
class SettingFinance extends \yii\db\ActiveRecord
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
            [['invoices_to_company_email', 'invoices_to_client_email'], 'default', 'value' => false]
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
}
