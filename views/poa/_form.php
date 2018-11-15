<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\XprofileXcompany */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="xprofile-xcompany-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_profile')->textInput() ?>

    <?= $form->field($model, 'id_company')->textInput() ?>

    <?= $form->field($model, 'job_post')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'url_form')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'url_upload_poa')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'url_poa')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'term_of_office')->textInput() ?>

    <?= $form->field($model, 'checked')->textInput() ?>

    <?= $form->field($model, 'STATUS_POA')->textInput() ?>

    <?= $form->field($model, 'comments')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
