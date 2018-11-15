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
    /**
    * @inheritdoc
    */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetAttribute' => 'email','targetClass' => '\app\models\User', 'message' => 'Пользователь с таким email уже зарегистрирован'],
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
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
            $this->scenario = [];
            return null;

        }
    }

}