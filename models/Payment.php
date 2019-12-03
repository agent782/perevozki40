<?php

namespace app\models;

use Yii;
use app\components\DateBehaviors;
/**
 * This is the model class for table "payment".
 *
 * @property int $id ID
 * @property double $cost Сумма
 * @property int $type Тип платежа
 * @property int $date Дата
 * @property int $id_user Пользователь (плательщик)
 * @property int $id_implementer Пользователь (получатель)
 * @property int $id_company Юр. лицо (плательщик
 * @property int $id_our_company Юр.лицо (получатель)
 * @property int $status Статус
 * @property string $comments Основание
 * @property string $sys_info
 * @property int $create_at
 * @property int $update_at
 * @property int $direction
 * @property int $calculation_with
 * @property int $id_request_payment
 */
class Payment extends \yii\db\ActiveRecord
{
    const TYPE_CASH = 1;
    const TYPE_SBERBANK_CARD = 2;
    const TYPE_BANK_TRANSFER = 3;

    const CALCULATION_WITH_CLIENT = 1;
    const CALCULATION_WITH_CAR_OWNER = 2;

    const CREDIT = 0;
    const DEBIT = 1;

    const STATUS_CANCELED = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_WAIT = 2;
    const STATUS_ERROR = 3;
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
            [['date', 'cost', 'type','id_user','status', 'direction', 'calculation_with'], 'required'],
            [['id_company', 'id_our_company'], 'validateTypeBankTransfer', 'skipOnEmpty' => false],
            [['cost'], 'number'],
            [['type','id_user', 'id_implementer', 'id_company',
                'id_our_company', 'status', 'direction', 'calculation_with'], 'integer'],
            [['comments', 'sys_info'], 'string'],
            [['date', 'create_at', 'update_at'], 'default', 'value' => date('d.m.Y')],
            [['date', 'create_at', 'update_at'], 'date', 'format' => 'php: d.m.Y'],
            ['id_request_payment', 'default', 'value' => null]
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
            'id_user' => 'Пользователь',
            'id_implementer' => 'Пользователь (получатель)',
            'id_company' => 'Контрагент',
            'id_our_company' => 'Наше юр. лицо',
            'status' => 'Статус',
            'comments' => 'Информация',
            'sys_info' => 'Sys Info',
            'create_at' => 'Create At',
            'update_at' => 'Update At',
            'direction' => 'Дебет / кредит',
            'calculation_with' => 'Расчет с',
        ];
    }

    public function behaviors()
    {
        return [
            'convertDate' => [
                'class' => DateBehaviors::class,
                'dateAttributes' => ['date', 'create_at', 'update_at'],
                'format' => DateBehaviors::FORMAT_DATE,
            ]
        ];
    }

    public function validateTypeBankTransfer($attribute){
        if($this->type == self::TYPE_BANK_TRANSFER
            && $this->calculation_with != $this::CALCULATION_WITH_CAR_OWNER
            && !$this->$attribute)
            $this->addError($attribute, 'Ошибка');
        return;
    }

    public function afterSave($insert, $changedAttributes)
    {
        if($this->type != Payment::TYPE_BANK_TRANSFER
            || $this->calculation_with == $this::CALCULATION_WITH_CAR_OWNER){

            $this->id_company = null;
        }
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }

    public function beforeSave($insert)
    {
        if($insert){
            $this->id_implementer = Yii::$app->user->id;
        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function getArrayStatuses(){
        return [
            self::STATUS_WAIT => 'В очереди',
            self::STATUS_SUCCESS => 'Выполнен',
            self::STATUS_CANCELED => 'Отменен',
            self::STATUS_ERROR => 'Ошибка'
        ];
    }

    public function getArrayCalculationWith(){
        return[
            self::CALCULATION_WITH_CLIENT => 'С клиентом',
            self::CALCULATION_WITH_CAR_OWNER => 'С водителем'
        ];
    }

    public function getProfile(){
        return $this->hasOne(Profile::class, ['id_user' => 'id_user']);
    }

    public function getCompany(){
        return $this->hasOne(Company::class, ['id' => 'id_company']);
    }

    public function getOurCompany(){
        return $this->hasOne(Company::class, ['id' => 'id_our_company']);
    }

    public function getTypePayment(){
        return $this->hasOne(TypePayment::class, ['id' => 'type']);
    }

}
