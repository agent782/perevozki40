<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Profile */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="profile-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_user')->textInput() ?>

    <?= $form->field($model, 'phone2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'surname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'patrinimic')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sex')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'photo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bithday')->textInput() ?>

    <?= $form->field($model, 'reg_address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status_client')->textInput() ?>

    <?= $form->field($model, 'status_vehicle')->textInput() ?>

    <?= $form->field($model, 'raiting_client')->textInput() ?>

    <?= $form->field($model, 'raiting_vehicle')->textInput() ?>

    <?= $form->field($model, 'id_passport')->textInput() ?>

    <?= $form->field($model, 'is_driver')->textInput() ?>

    <?= $form->field($model, 'id_driver_license')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
