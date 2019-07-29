<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 11.07.2019
 * Time: 13:57
 */

namespace app\models;


use yii\base\Model;

class ResetPasswordSmsForm extends Model
{
    private $_sms_code;
    public $phone;
    public $sms_code;
    public $password;
    public $repeat_password;
    public $captcha;

    public function rules()
    {
        return [
            [['sms_code', 'password', 'repeat_password', 'phone'], 'required'],
//            ['phone', 'exist', 'targetClass' => User::class, 'targetAttribute' => 'username'],
            ['repeat_password', 'compare', 'compareAttribute' => 'password'],
            ['captcha', 'captcha'],
//            ['sms_code', 'compare', 'compareAttribute' => '_sms_сode', 'message' => 'Код неверный!' . $this->_sms_code],
        ];
    }

    public function attributeLabels()
    {
        return [
            'phone' => 'Введите Ваш номер телефона',
            'captcha' => 'Введите код с картинки',
            'sms_code' => 'Код из СМС',
            'password' => 'Новый пароль',
            'repeat_password' => 'Пароль еще раз'

        ];
    }

    public function generate_code(){
        $this->_sms_code = (string)rand(1000, 9999);
    }

    public function set_sms_code($code){
        $this->_sms_code = $code;
    }

    public function get_sms_code(){
        return $this->_sms_code;
    }

    public function validSmsCode(){
        return $this->sms_code == $this->_sms_code;
    }
}