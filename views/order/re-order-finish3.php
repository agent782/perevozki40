<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 09.09.2019
 * Time: 13:01
 */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use app\models\Vehicle;
use app\models\Payment;
    $this->title = Html::encode('Регистрация и завершение повторного заказа.')

?>
<h4><?=$this->title?></h4>
<br><br>
<?php
    $form = ActiveForm::begin();
?>
<?//= $form->field($modelOrder, 'id_vehicle_type')->hiddenInput()->label(false)?>
<?//= $form->field($modelOrder, 'cost')->hiddenInput()->label(false)?>

<?= $form->field($modelOrder, 'comment')->textarea([
    'placeholder' => 'Заказчик, телефон, Организация ...'
])->label('Информация о Клиенте')?>
<?//= $form->field($modelOrder, 'comment_vehicle')->textarea([
//    'placeholder' => 'Комментарии, дополнительные расходы...'
//])?>
<?//= $CalculateAndPrintFinishCost['text']?>
<?php
//    if($modelOrder->id_vehicle_type == Vehicle::TYPE_SPEC && !$modelOrder->cost){
//        echo $form->field($modelOrder, 'cost_finish_vehicle')->input('tel')
//            ->label('Договорная стоимость за заказ при оплате наличными');
//    }
//?>
<br>
<?php
//    if($modelOrder->type_payment != Payment::TYPE_CASH):
//?>
<!--<comment class="font-italic">При безналичном рассчете Клиенту будет выставлен счет на сумму-->
<!--    --><?//=$modelOrder->cost_finish ?><!-- р.-->
<!--</comment>-->
<?php
//    endif;
//?>
<br>
<?= Html::a('Отмена', $redirect, ['class' => 'btn btn-warning']) ?>
<?= Html::submitButton('Завершить', [
    'class' => 'btn btn-success',
    'name' => 'button',
    'value' => 'next3'
]) ?>

<?php
    $form::end();
?>
