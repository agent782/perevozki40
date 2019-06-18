<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\settings\SettingSMS */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="setting-sms-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'last_num_contract')->textInput() ?>

    <?= $form->field($model, 'noPhotoPath')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'FLAG_EXPIRED_ORDER')->textInput() ?>

    <?= $form->field($model, 'user_discount_cash')->textInput() ?>

    <?= $form->field($model, 'client_discount_cash')->textInput() ?>

    <?= $form->field($model, 'vip_client_discount_cash')->textInput() ?>

    <?= $form->field($model, 'user_discount_card')->textInput() ?>

    <?= $form->field($model, 'client_discount_card')->textInput() ?>

    <?= $form->field($model, 'vip_client_discount_card')->textInput() ?>

    <?= $form->field($model, 'procent_vehicle')->textInput() ?>

    <?= $form->field($model, 'procent_vip_vehicle')->textInput() ?>

    <?= $form->field($model, 'sms_code_update_phone')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
