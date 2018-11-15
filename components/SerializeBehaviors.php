<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.08.2018
 * Time: 9:47
 */

namespace app\components;

use yii\db\ActiveRecord;
use yii\base\Behavior;

class SerializeBehaviors extends Behavior
{
    const FORMAT_DATE = 'd.m.Y';
    const FORMAT_DATETIME = 'd.m.Y H:m';
    public $dateAttributes = [];
    public $format = self::FORMAT_DATE; //по умолчанию

    public $arrAttributes = [];

    public function events()
    {
        return[
            ActiveRecord::EVENT_AFTER_FIND => 'Unserialize',
            ActiveRecord::EVENT_BEFORE_INSERT => 'Serialize',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'Serialize',
            ActiveRecord::EVENT_AFTER_INSERT => 'Unserialize',
            ActiveRecord::EVENT_AFTER_UPDATE => 'Unserialize',
        ];
    }

    public function Unserialize($events){
        foreach ($this->arrAttributes as $arrAttribute){
            if ($this->owner->$arrAttribute){
                $this->owner->$arrAttribute = unserialize($this->owner->$arrAttribute);
            }
        }
    }

    public function Serialize($events){

        foreach ($this->arrAttributes as $arrAttribute){
            if ($this->owner->$arrAttribute){
                $this->owner->$arrAttribute = serialize($this->owner->$arrAttribute);
            }
        }
    }

    public function ConvertToDate($event){
        foreach ($this->dateAttributes as $dateAttribute) {
            if ($this->owner->$dateAttribute) {
                $this->owner->$dateAttribute = date($this->format, $this->owner->$dateAttribute);
            }
        }
    }

    public function ConvertToUnix($event){
        foreach ($this->dateAttributes as $dateAttribute){
            if ($this->owner->$dateAttribute) {
                $this->owner->$dateAttribute = strtotime($this->owner->$dateAttribute);
            }
        }
    }
}