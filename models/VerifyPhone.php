<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 08.01.2018
 * Time: 13:33
 */

namespace app\models;


use yii\base\Model;

class VerifyPhone extends Model
{
    public $userCode;
    private $_verifyCode;

    public function rules(){
        return [
            [['userCode'], 'required'],
        ];
    }
    public function attributeLabels(){
        return[
            'userCode' => 'Код подтверждения из СМС',
        ];
    }

    public function generateCode(){
        $this->_verifyCode = rand(1000, 9999);
    }

    public function getVerifyCode(){
        return $this->_verifyCode;
    }

    public function checkUserCode(){
        return ($this->userCode == $this->getVerifyCode());
    }


}


