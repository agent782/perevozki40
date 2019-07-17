<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
* Signup form
*/
class SignupUserForm extends Model
{
    public $email;
    public $password;
    public $confidentiality_agreement;
    public $use_conditions;
    /**
    * @inheritdoc
    */
    public function rules()
    {
        return [
            ['email', 'trim'],
            [['password','email'], 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'skipOnEmpty' => false, 'skipOnError' => false,
                'targetAttribute' => 'email','targetClass' => '\app\models\User',
                'message' => 'Пользователь с таким email уже зарегистрирован'],
            ['password', 'string', 'min' => 6],
            [['confidentiality_agreement', 'use_conditions'],
                'compare', 'compareValue' => 1, 'operator' => '==', 'skipOnEmpty' => false, 'skipOnError' => false,
                'message' => 'Подтвердите согласие.'],
        ];
    }
    public function attributeLabels(){
        return[
            'email' => 'Адрес электронной почты',
            'password' => 'Пароль',
        ];
    }
    /**
    * Signs user up.
    *
    * @return User|null the saved model or null if saving fails
    */
    public function signup($user)
    {

        if (!$this->validate() || !$user) {
            return null;
        }

        $user->email = $this->email;

        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->scenario = $user::SCENARIO_SAVE;
        if($user->save()){
           $user->scenario = $user::SCENARIO_DEFAULT;
            return $user;
        } else {
            $this->scenario = self::SCENARIO_DEFAULT;
            return null;

        }
    }

}