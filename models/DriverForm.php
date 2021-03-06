<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.05.2018
 * Time: 10:46
 */

namespace app\models;

use Dadata\Response\Date;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\imagine\Image;

class DriverForm extends Model
{
    public $name;
    public $surname;
    public $patronymic;
    public $birthday;
    public $phone;
    public $phone2;
    public $address;
    public $country;
    public $number_passport;
    public $date_passport;
    public $place_passport;
    public $number_license;
    public $date_license;
    public $place_license;
    public $photo;

    public function rules()
    {
        return [
          [['name', 'surname', 'birthday', 'address', 'country', 'number_passport',
              'date_passport', 'place_passport', 'number_license',
              'date_license', 'place_license', 'phone'], 'required'],
            [['place_passport'], 'string', 'max' => 255],
            [['name', 'surname', 'patronymic', 'number_passport','number_license', 'phone2'], 'string', 'max' => 24],
            [['date_license', 'date_passport'], 'date', 'format' => 'php:d.m.Y', 'min' => (time() - 60*60*24*365*50),
                'max' => time(),
                'tooSmall' => 'Проверьте дату.',
                'tooBig' => 'Вы из будущего?)'],
            [['birthday'], 'date', 'format' => 'php:d.m.Y',
                'max' => (time() - 60*60*24*365*18), 'min' => (time() - 60*60*24*365*100),
                'tooBig' => 'Вам должно быть не менее 18 лет',
                'tooSmall' => 'Максимальный возраст - 100 лет'],
            [['photo'], 'image', 'extensions' => 'jpg, bmp', 'maxSize' => 6000000],
//            ['photo', 'file', 'maxSize' => 2100000, 'tooBig' => 'Максимальный допустимый размер файла 2Мб'],
            [['phone'], 'validateLengthPhone'],

        ];
    }

    public function beforeValidate()
    {
        $this->phone = mb_ereg_replace("[^0-9]",'',$this->phone);
        $this->phone2 = mb_ereg_replace("[^0-9]",'',$this->phone2);
        $this->number_passport = mb_ereg_replace("[^0-9]",'',$this->number_passport);
        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }

    public function validateLengthPhone(){
        $this->phone = mb_ereg_replace("[^0-9]",'',$this->phone);
        $len = mb_strlen($this->phone);
        if($len != 10 && $len != 11){
            $this->addError('phone', 'Неверный формат номера.');
        }
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'surname' => 'Фамилия',
            'patronymic' => 'Отчество',
            'birthday' => 'Дата рождения',
            'address' => 'Адрес регистрации',
            'country' => 'Гражданство',
            'number_passport' => 'Серия и номер паспорта',
            'date_passport' => 'Дата выдачи паспорта',
            'place_passport' => 'Кем выдан паспорт',
            'number_license' => 'Серия и номер водительского удостоверения (ВУ)',
            'date_license' => 'Дата выдачи ВУ',
            'place_license' => 'Кем выдано ВУ',
            'phone' => 'Номер телефона',
            'phone2' => 'Дополнительный номкр телефона',
            'photo' => 'Фото',

        ];
    }

    public function initDriverForm($modelDriver){
        if($modelDriver) {
            $this->name = $modelDriver->name;
            $this->surname = $modelDriver->surname;
            $this->patronymic = $modelDriver->patronymic;
            $this->birthday = $modelDriver->birthday;
            $this->address = $modelDriver->address;
            $this->phone = $modelDriver->phone;
            $this->phone2 = $modelDriver->phone2;
            $this->photo = $modelDriver->photo;

            $modelPassport = $modelDriver->passport;
            if($modelPassport){
                $this->country = $modelPassport->country;
                $this->number_passport = $modelPassport->number;
                $this->date_passport = $modelPassport->date;
                $this->place_passport = $modelPassport->place;
            }

            $modelDriverLicense = $modelDriver->license;
            if($modelDriverLicense){
                $this->number_license = $modelDriverLicense->number;
                $this->date_license = $modelDriverLicense->date;
                $this->place_license = $modelDriverLicense->place;
            }
        }

        return $this;
    }

    public function save($modelDriver, $id_car_owner){
        $modelPassport = $modelDriver->passport;
        if(!$modelPassport) $modelPassport = new Passport();

        $modelLicense = $modelDriver->license;
        if(!$modelLicense) $modelLicense = new DriverLicense();

        $modelDriver->name = $this->name;
        $modelDriver->surname = $this->surname;
        $modelDriver->patronymic = $this->patronymic;
        $modelDriver->birthday = $this->birthday;
        $modelDriver->address = $this->address;
        $modelDriver->phone = $this->phone;
        $modelDriver->phone2 = $this->phone2;
        $modelDriver->id_car_owner = $id_car_owner;

        if($modelDriver->save()){
            $this->photo = UploadedFile::getInstance($this, 'photo');
            if($this->photo) {
                $modelDriver->photo = $this->uploadPhoto($modelDriver->id);
            }

            $modelPassport->country = $this->country;
            $modelPassport->number = $this->number_passport;
            $modelPassport->date = $this->date_passport;
            $modelPassport->place = $this->place_passport;
            if($modelPassport->save()) $modelDriver->passport_id = $modelPassport->id;

            $modelLicense->number = $this->number_license;
            $modelLicense->date = $this->date_license;
            $modelLicense->place = $this->place_license;
            if($modelLicense->save()) $modelDriver->license_id = $modelLicense->id;

            if($modelDriver->save()) return $modelDriver;
        }
        return false;

    }

    public function uploadPhoto($idDriver){
        if($this->validate() && $this->photo){
            $dir = Yii::getAlias('@driverPhotoDir/');
            $filename = $idDriver . '.' . $this->photo->extension;
            $this->photo->saveAs($dir.$filename);
            Image::autorotate($dir.$filename)->save();
            Image::thumbnail($dir.$filename, 768, null)->save();
        }else{
            $filename = 'noPhoto.jpg';
        }
        return $filename;
    }
}