<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\VehicleSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vehicle-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'id_user') ?>

    <?= $form->field($model, 'tonnage') ?>

    <?= $form->field($model, 'length') ?>

    <?= $form->field($model, 'width') ?>

    <?php // echo $form->field($model, 'height') ?>

    <?php // echo $form->field($model, 'longlength') ?>

    <?php // echo $form->field($model, 'tonnage_long') ?>

    <?php // echo $form->field($model, 'length_long') ?>

    <?php // echo $form->field($model, 'passengers') ?>

    <?php // echo $form->field($model, 'ep') ?>

    <?php // echo $form->field($model, 'rp') ?>

    <?php // echo $form->field($model, 'lp') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
