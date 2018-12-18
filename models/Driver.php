<?php

namespace app\models;

use Yii;
use app\components\DateBehaviors;
use nickcv\encrypter\behaviors\EncryptionBehavior;
/**
 * This is the model class for table "drivers".
 *
 * @property integer $id
 * @property integer $id_car_owner
 * @property string $name
 * @property string $surname
 * @property string $patronymic
 * @property integer $birthday
 * @property string $address
 * @property integer $passport_id
 * @property integer $license_id
 * @property string $phone
 * @property string $phone2
 * @property integer $raiting
 * @property integer $checking
 * @property integer $create_at
 * @property string $fio
 * @property string $photo
 * @property object $passport
 * @property object $license
 * @property integer $status
 * @property string statusString
 */
class Driver extends \yii\db\ActiveRecord
{

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const STAUS_ARHIVE = 2;

    public $statuses = [
        'Удален',
        'Активен'
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'drivers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'surname', 'phone', 'address', 'birthday'], 'required'],
            [['passport_id', 'license_id', 'raiting', 'checking'], 'integer'],
            ['birthday', 'date' , 'format' => 'php:d.m.Y'],
            [['address'], 'string'],
            [['phone', 'phone2'], 'string', 'max' => 16],
            [['name', 'surname', 'patronymic'], 'string', 'max' => 32],
            ['checking', 'default', 'value' => 0],
            ['create_at', 'default', 'value' => date('d.m.Y')],
            ['photo', 'string', 'max' => 255, 'tooLong' => 'Слишком длинное название файла.'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['id_car_owner', 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'surname' => 'Фамилия',
            'patronymic' => 'Отчество',
            'birthday' => 'Дата рождения',
            'address' => 'Адрес регистрации',
            'passport_id' => 'Passport ID',
            'license_id' => 'License ID',
            'phone' => 'Номер телефона',
            'phone2' => 'Дополнительный номкр телефона',
            'raiting' => 'Рейтинг',
            'checking' => 'Проверен',
            'create_at' => 'Дата создания',
            'fio' => 'ФИО',
            'status' => 'Статус'
        ];
    }

    public function behaviors()
    {
        return [
            'encryption' => [
                'class' => '\nickcv\encrypter\behaviors\EncryptionBehavior',
                'attributes' => [
                    'address',
                ],
            ],
            'convertDate' => [
                'class' => 'app\components\DateBehaviors',
                'dateAttributes' => ['birthday', 'create_at'],
                'format' => DateBehaviors::FORMAT_DATE,
            ]
        ];
    }

    public function getPassport(){
        return $this->hasOne(Passport::className(), ['id'=>'passport_id']);
    }

    public function getLicense(){
        return $this->hasOne(DriverLicense::className(), ['id' => 'license_id']);
    }

    public function getFio(){
        return $this->surname. ' '. $this->name. ' '. $this->patronymic;
    }

    public function setStatus($status){
        if($this->status!=$status){
            $this->status = $status;
            return ($this->save()) ? true : false;
        }
        return false;
    }

    public function getStatusString(){
        switch ($this->status){
            case 0:
                return 'Удален';
            case 1:
                return 'Активен';
            default:
                return 'Не установлен';
        }
    }


}
