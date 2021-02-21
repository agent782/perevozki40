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

<?= $form->field($modelOrder, 'comment')->textarea([
    'placeholder' => 'Заказчик, телефон, Организация ...'
])->label('Информация о Клиенте')?>

<?= Html::a('Отмена', $redirect, ['class' => 'btn btn-warning']) ?>
<?= Html::submitButton('Завершить', [
    'class' => 'btn btn-success',
    'name' => 'button',
    'value' => 'next3'
]) ?>

<?php
    $form::end();
?>
