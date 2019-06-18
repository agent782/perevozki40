<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 18.06.2019
 * Time: 18:30
 */

namespace app\models;


use yii\base\Model;
use Yii;

class ChangePasswordForm extends Model
{
    public $old_pass;
    public $new_pass;
    public $new_pass_repeat;

    private $_user = false;

    public function rules()
    {
        return [
            ['new_pass', 'string', 'min' => 6, 'max' => 64, 'skipOnEmpty' => false],
            ['new_pass_repeat', 'compare', 'compareAttribute'  => 'new_pass', 'skipOnEmpty' => false],
            [['new_pass_repeat', 'old_pass'], 'required', 'skipOnEmpty' => false],
            ['old_pass', 'validatePassword', 'skipOnError' => true, 'skipOnEmpty' => false]
        ];
    }


    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->old_pass)) {
                $this->addError($attribute, 'Неверный  "Пароль".');
            }
        }
    }

    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = Yii::$app->user->identity;
        }

        return $this->_user;
    }
}