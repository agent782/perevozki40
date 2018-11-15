<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_service') ?>

    <?= $form->field($model, 'id_vehicle_type') ?>

    <?= $form->field($model, 'tonnage') ?>

    <?= $form->field($model, 'length') ?>

    <?= $form->field($model, 'width') ?>

    <?php // echo $form->field($model, 'height') ?>

    <?php // echo $form->field($model, 'volume') ?>

    <?php // echo $form->field($model, 'longlength') ?>

    <?php // echo $form->field($model, 'passengers') ?>

    <?php // echo $form->field($model, 'ep') ?>

    <?php // echo $form->field($model, 'rp') ?>

    <?php // echo $form->field($model, 'lp') ?>

    <?php // echo $form->field($model, 'tonnage_spec') ?>

    <?php // echo $form->field($model, 'length_spec') ?>

    <?php // echo $form->field($model, 'volume_spec') ?>

    <?php // echo $form->field($model, 'cargo') ?>

    <?php // echo $form->field($model, 'datetime_start') ?>

    <?php // echo $form->field($model, 'datetime_finish') ?>

    <?php // echo $form->field($model, 'datetime_access') ?>

    <?php // echo $form->field($model, 'valid_datetime') ?>

    <?php // echo $form->field($model, 'id_route') ?>

    <?php // echo $form->field($model, 'id_route_real') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
