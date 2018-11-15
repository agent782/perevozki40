<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 26.01.2018
 * Time: 14:30
 */

namespace app\components;


use yii\base\Behavior;
use yii\db\ActiveRecord;
use Yii;
use nickcv\encrypter\components\Encrypter;

class CryptBehaviors extends Behavior
{
    private $pass = 'k031208m';
    public $attrs = array();

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'encrypt',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'encrypt',
            ActiveRecord::EVENT_AFTER_FIND => 'decrypt',
        ];
    }

    public function encrypt($events){
        foreach ($this->attrs as $attr){
            $this->owner->$attr = \Yii::$app->encrypter->encrypt($this->owner->$attr);
            $attr = json_encode($attr, true);
            echo "<script>console.log('".$attr."');</script>";
        }
    }

    public function decrypt($events){
        foreach ($this->attrs as $attr){
            //РАботает только с двойным декодированием
            $this->owner->$attr = (\Yii::$app->encrypter->decrypt($this->owner->$attr));
        }
    }


}