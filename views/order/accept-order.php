<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 09.01.2019
 * Time: 15:02
 */
    use yii\bootstrap\ActiveForm;
    use yii\bootstrap\Html;
?>
<div class="col-lg-4">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($OrderModel, 'id_vehicle')->radioList($vehicles)->label('Выберите ТС')?>
    <?= $form->field($OrderModel, 'id_driver')->radioList($drivers)->label('Выберите водителя'); ?>
    <?php ActiveForm::end();?>
    <?= Html::a('Отменить', $redirect, ['class' => 'btn btn-warning'])?>
    <?= Html::submitButton('Принять и позвонить', ['class' => 'btn btn-primary'])?>
</div>
<div class="col-lg-8">

</div>

