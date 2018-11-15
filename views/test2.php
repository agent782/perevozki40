<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Order */
/* @var $form ActiveForm */
?>
<div class="test2">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'id_user') ?>
        <?= $form->field($model, 'datetime_start') ?>
        <?= $form->field($model, 'id_vehicle') ?>
        <?= $form->field($model, 'id_route') ?>
        <?= $form->field($model, 'id_route_real') ?>
        <?= $form->field($model, 'weight') ?>
        <?= $form->field($model, 'long') ?>
        <?= $form->field($model, 'width') ?>
        <?= $form->field($model, 'height') ?>
        <?= $form->field($model, 'ep') ?>
        <?= $form->field($model, 'rp') ?>
        <?= $form->field($model, 'lp') ?>
        <?= $form->field($model, 'id_vehicle_type') ?>
        <?= $form->field($model, 'longlenth') ?>
        <?= $form->field($model, 'date_create') ?>
        <?= $form->field($model, 'datetime_start_max') ?>
        <?= $form->field($model, 'date_start_max') ?>
        <?= $form->field($model, 'time_start_max') ?>
        <?= $form->field($model, 'volume') ?>
        <?= $form->field($model, 'id_tariff') ?>
        <?= $form->field($model, 'cargo') ?>
    
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- test2 -->
