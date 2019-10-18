<?php

namespace app\models;

use Yii;
use app\components\DateBehaviors;

/**
 * This is the model class for table "request_payment".
 *
 * @property int $id
 * @property int $id_user
 * @property double $cost
 * @property int $type_payment
 * @property string $requisites
 * @property string $url_files
 * @property int $status
 * @property int $create_at
 * @property string statusText
 */
class RequestPayment extends \yii\db\ActiveRecord
{
    const STATUS_NEW = 1;
    const STATUS_OK = 2;
    const STATUS_ERROR = 3;
    const STATUS_CANCEL = 4;

    public $file;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'request_payment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cost', 'requisites'], 'required'],
            [['id_user', 'type_payment', 'status'], 'integer'],
            [['cost'], 'number'],
            [['requisites', 'url_files'], 'string'],
            ['create_at', 'default', 'value' => date('d.m.Y H:i')],
            ['status', 'default', 'value' => self::STATUS_NEW],
            ['file', 'file', 'maxSize' => 7000000]
        ];
    }
    public function behaviors()
    {
        return [
            'convertDateTime' => [
                'class' => 'app\components\DateBehaviors',
                'dateAttributes' => ['create_at'],
                'format' => DateBehaviors::FORMAT_DATETIME,
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_user' => 'Id User',
            'cost' => 'Сумма',
            'type_payment' => 'Тип оплаты',
            'requisites' => 'Реквизиты получателя',
            'url_files' => 'Url Files',
            'status' => 'Статус',
            'statusText' => 'Статус',
            'create_at' => 'Дата',
            'file' => 'Скан счета на оплату (при оплате на р/с ИП или ООО)'
        ];
    }

    public function getStatusText(){
        switch ($this->status){
            case self::STATUS_NEW:
                return 'В обработке.';
                break;
            case self::STATUS_OK:
                return 'Выполнено.';
                break;
            case self::STATUS_CANCEL:
                return 'Отменено.';
                break;
            case self::STATUS_ERROR:
                return 'Ошибка.';
                break;
        }
        return false;
    }

    public function getTypePaymentText($short = false){
        $type_payment = TypePayment::findOne($this->type_payment);
        return ($short)?$type_payment->min_text:$type_payment->type;
    }
}
