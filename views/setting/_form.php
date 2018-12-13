<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
/* @var $this yii\web\View */
/* @var $model app\models\Setting */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="setting-form">

    <?php $form = ActiveForm::begin([
                'layout' => 'horizontal'
            ]); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'last_num_contract')->textInput() ?>

    <?= $form->field($model, 'id_our_company')->textInput() ?>

    <?= $form->field($model, 'noPhotoPath')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'FLAG_EXPIRED_ORDER')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
