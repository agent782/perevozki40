<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 11.01.2018
 * Time: 10:26
 */

namespace app\models;


use yii\base\Model;

class SignupPhoneForm extends Model
{
    public $phone;

    public function rules(){
        return [
            ['phone', 'unique', 'targetAttribute' => 'username','targetClass' => '\app\models\User',  'message' => 'Пользователь с таким номером телефона уже зарегистрирован']
        ];
    }
}