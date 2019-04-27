<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProfileSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="profile-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_user') ?>

    <?= $form->field($model, 'phone2') ?>

    <?= $form->field($model, 'email2') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'surname') ?>

    <?php // echo $form->field($model, 'patrinimic') ?>

    <?php // echo $form->field($model, 'sex') ?>

    <?php // echo $form->field($model, 'photo') ?>

    <?php // echo $form->field($model, 'bithday') ?>

    <?php // echo $form->field($model, 'reg_address') ?>

    <?php // echo $form->field($model, 'status_client') ?>

    <?php // echo $form->field($model, 'status_vehicle') ?>

    <?php // echo $form->field($model, 'raiting_client') ?>

    <?php // echo $form->field($model, 'raiting_vehicle') ?>

    <?php // echo $form->field($model, 'id_passport') ?>

    <?php // echo $form->field($model, 'is_driver') ?>

    <?php // echo $form->field($model, 'id_driver_license') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
