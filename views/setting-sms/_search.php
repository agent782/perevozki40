<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\settings\SettingSMSSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="setting-sms-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'last_num_contract') ?>

    <?= $form->field($model, 'noPhotoPath') ?>

    <?= $form->field($model, 'FLAG_EXPIRED_ORDER') ?>

    <?= $form->field($model, 'user_discount_cash') ?>

    <?php // echo $form->field($model, 'client_discount_cash') ?>

    <?php // echo $form->field($model, 'vip_client_discount_cash') ?>

    <?php // echo $form->field($model, 'user_discount_card') ?>

    <?php // echo $form->field($model, 'client_discount_card') ?>

    <?php // echo $form->field($model, 'vip_client_discount_card') ?>

    <?php // echo $form->field($model, 'procent_vehicle') ?>

    <?php // echo $form->field($model, 'procent_vip_vehicle') ?>

    <?php // echo $form->field($model, 'sms_code_update_phone') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
