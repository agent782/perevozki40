<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Company */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="company-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'inn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address_real')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address_post')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address_value')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'branch_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'capital')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email3')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'kpp')->textInput() ?>

    <?= $form->field($model, 'management_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'management_post')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name_full')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name_short')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ogrn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ogrn_date')->textInput() ?>

    <?= $form->field($model, 'okpo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'okved')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'opf_short')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone3')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'citizenship')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'state_actuality_date')->textInput() ?>

    <?= $form->field($model, 'state_registration_date')->textInput() ?>

    <?= $form->field($model, 'state_liquidation_date')->textInput() ?>

    <?= $form->field($model, 'state_status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'data_type')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'raiting')->textInput() ?>

    <?= $form->field($model, 'FIO_contract')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'basis_contract')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'job_contract')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
