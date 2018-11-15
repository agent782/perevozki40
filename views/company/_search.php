<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CompanySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="company-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'inn') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'address') ?>

    <?= $form->field($model, 'address_real') ?>

    <?php // echo $form->field($model, 'address_post') ?>

    <?php // echo $form->field($model, 'value') ?>

    <?php // echo $form->field($model, 'address_value') ?>

    <?php // echo $form->field($model, 'branch_type') ?>

    <?php // echo $form->field($model, 'capital') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'email2') ?>

    <?php // echo $form->field($model, 'email3') ?>

    <?php // echo $form->field($model, 'kpp') ?>

    <?php // echo $form->field($model, 'management_name') ?>

    <?php // echo $form->field($model, 'management_post') ?>

    <?php // echo $form->field($model, 'name_full') ?>

    <?php // echo $form->field($model, 'name_short') ?>

    <?php // echo $form->field($model, 'ogrn') ?>

    <?php // echo $form->field($model, 'ogrn_date') ?>

    <?php // echo $form->field($model, 'okpo') ?>

    <?php // echo $form->field($model, 'okved') ?>

    <?php // echo $form->field($model, 'opf_short') ?>

    <?php // echo $form->field($model, 'phone') ?>

    <?php // echo $form->field($model, 'phone2') ?>

    <?php // echo $form->field($model, 'phone3') ?>

    <?php // echo $form->field($model, 'citizenship') ?>

    <?php // echo $form->field($model, 'state_actuality_date') ?>

    <?php // echo $form->field($model, 'state_registration_date') ?>

    <?php // echo $form->field($model, 'state_liquidation_date') ?>

    <?php // echo $form->field($model, 'state_status') ?>

    <?php // echo $form->field($model, 'data_type') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'raiting') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'FIO_contract') ?>

    <?php // echo $form->field($model, 'basis_contract') ?>

    <?php // echo $form->field($model, 'job_contract') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
