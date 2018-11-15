<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 16.10.2018
 * Time: 12:11
 */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
echo $modelOrder->id_vehicle_type;
//var_dump($VehicleAttributes);
//var_dump($modelOrder);
?>
<div class="container">
<h4>Необходимые характеристики ТС и груза.</h4>

<?php
    $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
        'validationUrl' => '/order/validate-order',
    ]);
?>

    <?= $form->field($modelOrder, 'id_vehicle_type')->hiddenInput()->label(false)?>
    <?php
        $modelOrder->body_typies = $modelOrder->body_typies;
//    if($modelOrder->id_vehicle_type == \app\models\Vehicle::TYPE_SPEC)
//        echo $form->field($modelOrder, 'body_typies[]')->checkboxList($modelOrder->body_typies)->label(false)
    ?>

    <?php if($modelOrder->id_vehicle_type == \app\models\Vehicle::TYPE_TRUCK) {
            echo $form->field($modelOrder, 'longlength')->radioList(['Нет', 'Да'], ['value' => 0])->label(
            'Груз длинномер ' . \app\components\widgets\ShowMessageWidget::widget([
                'helpMessage' => '',
                'ToggleButton' => ['label' => '<img src="/img/icons/help-25.png">', 'class' => 'btn'],
            ])
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
                'type' => 'tel',
                'style' => 'width: 150px'
            ]
        ]);
    }
?>

<?= Html::a('Отмена', '/order', ['class' => 'btn btn-warning']) ?>

<?= Html::submitButton('Далее', [
    'class' => 'btn btn-success',
    'name' => 'button',
    'value' => 'next3'
])?>

<?php
    ActiveForm::end();
?>
</div>