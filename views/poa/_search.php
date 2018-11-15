<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SearchXprofileXcompany */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="xprofile-xcompany-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_profile') ?>

    <?= $form->field($model, 'id_company') ?>

    <?= $form->field($model, 'job_post') ?>

    <?= $form->field($model, 'url_form') ?>

    <?= $form->field($model, 'url_upload_poa') ?>

    <?php // echo $form->field($model, 'url_poa') ?>

    <?php // echo $form->field($model, 'term_of_office') ?>

    <?php // echo $form->field($model, 'checked') ?>

    <?php // echo $form->field($model, 'STATUS_POA') ?>

    <?php // echo $form->field($model, 'comments') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
