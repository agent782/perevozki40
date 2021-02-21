<?php

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 19.01.2018
 * Time: 9:12
 */
namespace app\models;
use app\models\Passport;
use app\models\Profile;
use yii\base\Model;
use Yii;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use yii\imagine\Image;
use yii\bootstrap\Html;


class UpdateUserProfileForm extends Model
{
    public $id_user;
    public $name;
    public $surname;
    public $patrinimic;
    public $email;
//    public $id_passport;
    public $sex;

    public $bithday;
    public $photo;
    public $passport_number;
    public $passport_date;
    public $passport_place;
    public $email2;
    public $phone2;
    public $country;
    public $reg_address;


    public function rules()
    {
        return [
            [['name', 'surname', 'patrinimic', 'bithday', 'email'], 'required'],
            [['passport_number', 'passport_date', 'passport_place', 'reg_address'], 'validatePassport', 'skipOnEmpty' =>false],
            ['phone2',  'string', 'length' => [10], 'message' => 'Некорректный номер', 'tooLong' => 'Некорректный номер','tooShort' => 'Некорректный номер',],
            [['country', 'sex', 'id_user'], 'safe'],
            ['passport_place', 'string', 'length' => [10, 100]],
//            ['passport_number', 'unique', 'targetClass' => 'app\models\Passport', 'targetAttribute' => 'id', 'message' => 'Такой паспорт уже заренистрирован в системе'],
            [['photo'], 'file', 'extensions' => 'jpg, jpeg', 'maxSize' => 8100000],
            [['passport_number', 'reg_address'], 'string', 'max' => 255],
            [['email','email2'], 'email'],
            [['bithday'], 'date', 'format' => 'php:d.m.Y',
                'max' => (time() - 60*60*24*365*18), 'min' => (time() - 60*60*24*365*100),
                'tooBig' => 'Вам должно быть не менее 18 лет',
                'tooSmall' => 'Максимальный возраст - 100 лет'],
            [['passport_date'], 'date', 'format' => 'php:d.m.Y',
                'min' => (time() - 60*60*24*365*50),
                'max' => time(),
                'tooSmall' => 'Проверьте дату.',
                'tooBig' => 'Вы из будущего?)'],
            ['email', 'validateUniqueEmail'],

//            ['email', 'validateEmail', 'skipOnEmpty' => false]
//            ['bithday', 'date', 'max' => (time() - 60*60*24*365*18)],

        ];
    }

    public function attributeLabels()
    {
        return [
            'name'=>'Имя',
            'surname'=>'Фамилия',
            'patrinimic'=>'Отчество',
            'email'=>'E-mail',
            'sex'=>'Пол',
            'bithday' => 'Дата рождения',
            'passport_number' => 'Номер паспорта',
            'passport_date' => 'Дата выдачи',
            'passport_place' => 'Кем выдан',
            'photo' => 'Фото профиля',
            'phone2' => 'Дополнительный телефон',
            'email2' => 'Дополнительный email',
            'country' => 'Гражданство',
            'reg_address' => 'Адрес регистрации'
        ];
    }

    public function validatePassport($attribute){
        if(($this->passport_number || $this->passport_date || $this->passport_place || $this->reg_address)
            && !$this->$attribute){
            $this->addError($attribute, 'Необходимо заполнить все данные паспорта или оставить все поля пустыми');
        }
    }

    public function validateUniqueEmail($attribute){
        if(User::find()->where(['email' => $this->$attribute])
            ->andWhere(['<>', 'id' , $this->id_user])->one()
        )
            $this->addError($attribute, 'Пользователь с таким email уже существует. Укажите другой адрес или войдите под пользователем с этим email');
    }

    public function setAttr(Profile $profile){
        $this->id_user = $profile->id_user;
        $this->name = $profile->name;
        $this->patrinimic = $profile->patrinimic;
        $this->surname = $profile->surname;
        $this->email = $profile->email;
        $this->email2 = $profile->email2;
        $this->reg_address = $profile->reg_address;
        if($profile->passport){
//            $this->id_passport = $profile->id_passport;
            $this->passport_number = $profile->passport->number;
            $this->passport_date = $profile->passport->date;
            $this->passport_place = $profile->passport->place;
            $this->country = $profile->passport->country;
        }
        $this->bithday = $profile->bithday;
        $this->sex = $profile->sex;
        $this->phone2 = $profile->phone2;
        $this->photo = $profile->photo;
    }

    public function sendToCheck(Profile $profile){
        if(!$profile) return false;

        if($profile->photo != $this->photo) {
            $this->photo = self::uploadPhoto(true);
        }
        $profile->update_to_check = $this->attributes;
        $profile->check_update_status = Profile::CHECK_UPDATE_STATUS_WAIT;
        if($profile->save()) return true;
        return false;
    }

//    public function saveProfile(Profile $profile){
//        // НЕ ДОДЕЛАНО!!!!!
//
//        if(!$profile) return false;
//
//
//        $profile->name = $this->name;
//        $profile->patrinimic = $this->patrinimic;
//        $profile->surname = $this->surname;
//        $profile->email = $this->email;
//        $profile->email2 = $this->email2;
//        $profile->reg_address = $this->reg_address;
//        if($profile->passport){
//            $passport = $profile->passport;
//        } else {
//            $passport = new Passport();
//        }
//        $passport->number = $this->passport_number;
//        $passport->date = $this->passport_date;
//        $passport->place = $this->passport_place;
//        $passport->country = $this->country;
//        $passport->save(false);
//
//        $profile->id_passport = $passport->id;
//        $profile->bithday = $this->bithday;
//        $profile->sex = $this->sex;
//        $profile->phone2 = $this->phone2;
//        if($this->photo){
//
//        }
//
//    }

    public function uploadPhoto($update = true){
        if($this->validate() && $this->photo){
            $dir = (!$update)
                ? Yii::getAlias('@userPhotoDir').'/'
                : Yii::getAlias('@userUpdatePhotoDir').'/'
            ;
            $filename = ($update)
                ?Yii::$app->user->getId() . '_upd.' . $this->photo->extension
                :Yii::$app->user->getId() . '.' . $this->photo->extension;
            
            $this->photo->saveAs($dir.$filename);
            Image::autorotate($dir.$filename)->save();
            Image::thumbnail($dir.$filename, 768, null)->save();

            return $filename;
        }else{
            return null;
        }
    }

    public function addPassport()
    {

        $modelPassport = new Passport();
        $modelPassport->number = $this->passport_number;
        $modelPassport->date = $this->passport_date;
        $modelPassport->place = $this->passport_place;
        $modelPassport->country = $this->country;
        if ($modelPassport->save()) {
            return $modelPassport;
        }
        return false;
    }

}

