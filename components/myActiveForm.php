<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 31.05.2019
 * Time: 11:26
 */

namespace app\components;


use yii\bootstrap\ActiveForm;

class myActiveForm extends ActiveForm
{
    public $fieldClass = myActiveField::class;

}