<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\RouteSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="route-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'startCity') ?>

    <?= $form->field($model, 'finishCity') ?>

    <?= $form->field($model, 'routeStart') ?>

    <?= $form->field($model, 'route1') ?>

    <?php // echo $form->field($model, 'route2') ?>

    <?php // echo $form->field($model, 'route3') ?>

    <?php // echo $form->field($model, 'route4') ?>

    <?php // echo $form->field($model, 'route5') ?>

    <?php // echo $form->field($model, 'route6') ?>

    <?php // echo $form->field($model, 'route7') ?>

    <?php // echo $form->field($model, 'route8') ?>

    <?php // echo $form->field($model, 'routeFinish') ?>

    <?php // echo $form->field($model, 'distance') ?>

    <?php // echo $form->field($model, 'count') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
