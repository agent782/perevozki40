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
    public $_verifyCode;


    public function rules(){
        return [
//            [['userCode'],'required'],
            [['userCode'],'compare', 'compareValue' => $this->_verifyCode, 'skipOnEmpty' => false, 'message' => 'Код не верный!'] ,
//            ['userCode', 'number'],

        ];
    }
    public function attributeLabels(){
        return[
            'userCode' => 'Код подтверждения из СМС',
        ];
    }

    public function validateCode($attribute, $params){
        if($this->$attribute != $this->_verifyCode)
            $this->addError($attribute, 'Код неверный');
            return;
    }


    public function generateCode(){
        $this->_verifyCode = (string)rand(1000, 9999);
    }

    public function getVerifyCode(){
        return $this->_verifyCode;
    }

    public function checkUserCode(){
        return ($this->userCode == $this->getVerifyCode());
    }

}



