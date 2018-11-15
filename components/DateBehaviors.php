<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 30.01.2018
 * Time: 10:39
 */

namespace app\components;

use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\db\ActiveRecordInterface;

class DateBehaviors extends Behavior
{
    const FORMAT_DATE = 'd.m.Y';
    const FORMAT_DATETIME = 'd.m.Y H:i';
    public $dateAttributes = [];
    public $format = self::FORMAT_DATE; //по умолчанию
    public function events()
    {
        return[
            ActiveRecord::EVENT_AFTER_FIND => 'ConvertToDate',
            ActiveRecord::EVENT_BEFORE_INSERT => 'ConvertToUnix',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'ConvertToUnix',
            ActiveRecord::EVENT_AFTER_INSERT => 'ConvertToDate',
            ActiveRecord::EVENT_AFTER_UPDATE => 'ConvertToDate',
        ];
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