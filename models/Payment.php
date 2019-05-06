<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "payment".
 *
 * @property int $id ID
 * @property double $cost Сумма
 * @property int $type Тип платежа
 * @property int $date Дата
 * @property int $id_payer_user Пользователь (плательщик)
 * @property int $id_recipient_user Пользователь (получатель)
 * @property int $id_payer_company Юр. лицо (плательщик
 * @property int $id_recipient_company Юр.лицо (получатель)
 * @property int $status Статус
 * @property string $comments Основание
 * @property string $sys_info
 * @property int $create_at
 * @property int $update_at
 * @property int $direction
 */
class Payment extends \yii\db\ActiveRecord
{
    const TYPE_CASH = 1;
    const TYPE_SBERBANK_CARD = 2;
    const TYPE_BANK_TRANSFER = 3;

    const DEBIT = 1;
    const CREDIT = 0;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cost'], 'number'],
            [['type','id_payer_user', 'id_recipient_user', 'id_payer_company',
                'id_recipient_company', 'status', 'direction'], 'integer'],
            [['comments', 'sys_info'], 'string'],
            [['date', 'create_at', 'update_at'], 'default', 'value' => date('d.m.Y')],
            [['date', 'create_at', 'update_at'], 'date', 'format' => 'php: d.m.Y'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cost' => 'Сумма',
            'type' => 'Тип оплаты',
            'date' => 'Дата',
            'id_payer_user' => 'Пользователь (плательщик)',
            'id_recipient_user' => 'Пользователь (получатель)',
            'id_payer_company' => 'Юр. лицо (плательщик)',
            'id_recipient_company' => 'Юр. лицо (получатель)',
            'status' => 'Статус',
            'comments' => 'Информация',
            'sys_info' => 'Sys Info',
            'create_at' => 'Create At',
            'update_at' => 'Update At',
            'direction' => 'Дебет / кредит'
        ];
    }

}
