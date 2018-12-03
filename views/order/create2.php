<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 16.10.2018
 * Time: 10:26
 */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
$this->registerJsFile('/js/order.js');
?>
<div class="container">
<?php

    $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
        'validationUrl' => '/order/validate-order',
    ]);
    echo $form->field($modelOrder, 'id_vehicle_type')->hiddenInput()->label(false);

//    $modelOrder->loading_typies[0] = '2';
//    var_dump($modelOrder);
?>

<?=
($modelOrder->id_vehicle_type != \app\models\Vehicle::TYPE_SPEC)
    ?
    $form->field($modelOrder, 'body_typies[]')->checkboxList(
        ArrayHelper::map($BTypies, 'id', 'body'), ['id' => 'chkBodyTypies'])
    :
    $form->field($modelOrder, 'body_typies[]')->radioList(
        ArrayHelper::map($BTypies, 'id', 'body'))
?>
<?php
    if($LTypies){
       echo $form->field($modelOrder, 'loading_typies[]')->checkboxList(ArrayHelper::map($LTypies, 'id', 'type'),
            [
                'id' => 'chkLoadingTypies',
                'value' => [2]
            ])->label('Необходимый тип погрузки/выгрузки.')->hint('Выбирайте дополнительные типы погрузки только при необходимости!');
    }
    ?>
<?=
Html::a('Отмена', '/order/client', ['class' => 'btn btn-warning'])
?>

<?= Html::submitButton('Далее', [
    'class' => 'btn btn-success',
    'name' => 'button',
    'value' => 'next2'
])?>


<?php
    ActiveForm::end();
?>
</div>