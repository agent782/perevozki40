<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 09.11.2020
 * Time: 16:35
 */

namespace app\models;

use Yii;


use yii\base\Model;

class MailingForm extends Model
{
    const SEND_TO_USERS = 1;

    public $from;
    public $to;
    public $subject;
    public $text;

    public function attributeLabels()
    {
        return [
            'from' => 'От кого',
            'to' => 'Кому',
            'subject' => 'Тема',
            'text' => 'Текст'

        ];
    }
}

