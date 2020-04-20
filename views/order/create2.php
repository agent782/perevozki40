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
?>
<?= var_dump($modelOrder->body_typies)?>
<?=
($modelOrder->id_vehicle_type != \app\models\Vehicle::TYPE_SPEC)
    ?
    $form->field($modelOrder, 'body_typies[]')->checkboxList(
        $BTypies, [
            'id' => 'chkBodyTypies',
            'encode' => false,
            'value' => $modelOrder->body_typies
        ])
    ->hint('Чем больше типов кузова выбрано, тем больше вероятность подбора ТС')
    :
    $form->field($modelOrder, 'body_typies[]')->radioList($BTypies,
        [
            'encode' => false,
            'value' => $modelOrder->body_typies
        ])
?>
<?php
    if($LTypies){
        $LTvalues = ($modelOrder->loading_typies)?$modelOrder->loading_typies:[2];
       echo $form->field($modelOrder, 'loading_typies[]')->checkboxList($LTypies,
            [
                'id' => 'chkLoadingTypies',
                'value' => $LTvalues,
                'encode' => false,
            ])->label('Необходимый тип погрузки/выгрузки.')
           ->hint('Выбирайте дополнительные типы погрузки только при необходимости!');
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