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
    public $name;
    public $surname;
    public $patrinimic;
    public $email;
    public $id_passport;
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
            [['bithday'], 'required'],
//            ['phone2', 'match', 'pattern' => '/^\+7\([0-9]{3}\)[0-9]{3}\-[0-9]{2}\-[0-9]{2}$/'],
            ['phone2',  'string', 'length' => [10], 'message' => 'Некорректный номер', 'tooLong' => 'Некорректный номер','tooShort' => 'Некорректный номер',],
            ['country', 'safe'],
            ['passport_place', 'string', 'length' => [10, 100]],
//            ['passport_number', 'unique', 'targetClass' => 'app\models\Passport', 'targetAttribute' => 'id', 'message' => 'Такой паспорт уже заренистрирован в системе'],
            [['photo'], 'image', 'extensions' => 'jpg', 'maxSize' => 4100000],
            [['passport_number', 'reg_address'], 'string', 'max' => 255],
            ['email2', 'email'],
            [['bithday'], 'date', 'format' => 'php:d.m.Y',
                'max' => (time() - 60*60*24*365*18), 'min' => (time() - 60*60*24*365*100),
                'tooBig' => 'Вам должно быть не менее 18 лет',
                'tooSmall' => 'Максимальный возраст - 100 лет'],
            [['passport_date'], 'date', 'format' => 'php:d.m.Y',
                'min' => (time() - 60*60*24*365*50),
                'max' => time(),
                'tooSmall' => 'Проверьте дату.',
                'tooBig' => 'Вы из будущего?)'],
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

    public function setAttr(Profile $profile){
        $this->name = $profile->name;
        $this->patrinimic = $profile->patrinimic;
        $this->surname = $profile->surname;
        $this->email = $profile->email;
        $this->email2 = $profile->email2;
        $this->reg_address = $profile->reg_address;
        if($profile->passport){
            $this->id_passport = $profile->id_passport;
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

    public function uploadPhoto(){
        if($this->validate() && $this->photo){
            $dir = Yii::getAlias('@userPhotoDir').'/';
            $filename = Yii::$app->user->getId() . '.' . $this->photo->extension;
            $this->photo->saveAs($dir.$filename);
            Image::thumbnail($dir.$filename, 768, null)->save();
            return $filename;
        }else{
            return null;

        }
    }


    public function saveProfile()
    {
        $modelProfile = new Profile();
        $modelProfile = Yii::$app->user->identity->profile;
        $this->photo = UploadedFile::getInstance($this, 'photo');
        if($this->photo) {
            $modelProfile->photo = $this->uploadPhoto();
        }
        $modelProfile->phone2 = $this->phone2;
        $modelProfile->email2 = $this->email2;
        $modelProfile->bithday = ($this->bithday);
        $modelProfile->reg_address = $this->reg_address;

        if($modelProfile->save()) {
            $auth = Yii::$app->authManager;
            $role = $auth->getRole('client');
            $auth->revokeAll($modelProfile->id_user);
            $auth->assign($role, $modelProfile->id_user);
            $modelPassport = $this->addPassport();
            if($modelPassport) {
                $modelProfile->id_passport = $modelPassport->id;
                if($modelProfile->save()) {
                    return $modelProfile;
                }
            }
        }
//        return $modelProfile;
        return false;
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

