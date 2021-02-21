<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 25.01.2021
 * Time: 15:41
 */
use yii\bootstrap\ActiveForm;

$form = new ActiveForm([
    'id' => $form_id
]);

//echo $i;

echo $form->field($model, 'invoices')
    ->checkboxList($invoices, [
        'encode' => false
    ])->label('Неоплаченные счета');