<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 16.10.2018
 * Time: 12:11
 */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use app\models\Vehicle;
//var_dump($VehicleAttributes);
?>

<div class="container">
<h4>Необходимые характеристики ТС и груза.</h4>

<?php
    $form = \app\components\myActiveForm::begin([
        'enableAjaxValidation' => true,
        'validationUrl' => '/order/validate-order',
    ]);
?>
    <?= $form->field($modelOrder, 'id_vehicle_type')->hiddenInput()->label(false)?>
    <?php
        if(array_key_exists(1, $modelOrder->body_typies) && $modelOrder->id_vehicle_type == Vehicle::TYPE_SPEC) {
            echo $form->field($modelOrder, 'body_typies[1]')->hiddenInput()->label(false);
        }
    ?>
    <?php
//        $modelOrder->body_typies = $modelOrder->body_typies;
//    if($modelOrder->id_vehicle_type == \app\models\Vehicle::TYPE_SPEC)
//        echo $form->field($modelOrder, 'body_typies[]')->checkboxList($modelOrder->body_typies)->label(false)
    ?>

    <?php if($modelOrder->id_vehicle_type == \app\models\Vehicle::TYPE_TRUCK) {
            echo $form->field($modelOrder, 'longlength')->radioList(['Нет', 'Да'], ['value' => 0])->label(
            'Груз длинномер', ['withTip' => true]
            );
            echo $form->field($modelOrder, 'cargo')->textarea([
                'placeholder' => '20 коробок 30х30х30см....Холодильник........Станок 1,5 х 1,5 х 1,5м'
            ]);

        }
    if($modelOrder->id_vehicle_type == \app\models\Vehicle::TYPE_PASSENGER){
        echo $form->field($modelOrder, 'cargo')->textarea([
            'placeholder' => 'Детская коляска'
        ]);
    }
    if($modelOrder->id_vehicle_type == \app\models\Vehicle::TYPE_SPEC){
        echo $form->field($modelOrder, 'cargo')->textarea()->label('Описание работ');
    }

    ?>
    <?php
    foreach ($VehicleAttributes as $attribute){
        echo $form->field($modelOrder, $attribute, [
            'inputOptions' => [
                'type' => 'text',
                'style' => 'width: 150px'
            ]
        ]);
    }
?>

<?= Html::a('Отмена', '/order/client', ['class' => 'btn btn-warning']) ?>

<?= Html::submitButton('Далее', [
    'class' => 'btn btn-success',
    'name' => 'button',
    'value' => 'next3'
])?>

<?php
\app\components\myActiveForm::end();
?>
</div>