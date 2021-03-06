<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CalendarVehicle */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="calendar-vehicle-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_vehicle')->textInput() ?>

    <?= $form->field($model, 'date')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
