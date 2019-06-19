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

class SerializeAndCryptBehaviors extends Behavior
{
    public $attrs = array();

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'encrypt',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'encrypt',
            ActiveRecord::EVENT_AFTER_FIND => 'decrypt',
            ActiveRecord::EVENT_AFTER_INSERT => 'decrypt',
            ActiveRecord::EVENT_AFTER_UPDATE => 'decrypt',
            ActiveRecord::EVENT_AFTER_REFRESH => 'decrypt',
        ];
    }

    public function encrypt($events){
        foreach ($this->attrs as $attr){
            if ($this->owner->$attr){
                $this->owner->$attr = serialize($this->owner->$attr);
                $this->owner->$attr = \Yii::$app->encrypter->encrypt($this->owner->$attr);
//                $attr = json_encode($attr, true);
//                echo "<script>console.log('".$attr."');</script>";
            }
        }
    }

    public function decrypt($events){
        foreach ($this->attrs as $attr){
            if ($this->owner->$attr){
                $this->owner->$attr = (\Yii::$app->encrypter->decrypt($this->owner->$attr));
                $this->owner->$attr = unserialize($this->owner->$attr);
            }
        }
    }

}