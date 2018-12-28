<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'id_user') ?>

    <?= $form->field($model, 'id_company') ?>

    <?= $form->field($model, 'id_vehicle_type') ?>

    <?= $form->field($model, 'tonnage') ?>

    <?php // echo $form->field($model, 'length') ?>

    <?php // echo $form->field($model, 'width') ?>

    <?php // echo $form->field($model, 'height') ?>

    <?php // echo $form->field($model, 'volume') ?>

    <?php // echo $form->field($model, 'longlength') ?>

    <?php // echo $form->field($model, 'passengers') ?>

    <?php // echo $form->field($model, 'ep') ?>

    <?php // echo $form->field($model, 'rp') ?>

    <?php // echo $form->field($model, 'lp') ?>

    <?php // echo $form->field($model, 'tonnage_spec') ?>

    <?php // echo $form->field($model, 'length_spec') ?>

    <?php // echo $form->field($model, 'volume_spec') ?>

    <?php // echo $form->field($model, 'cargo') ?>

    <?php // echo $form->field($model, 'datetime_start') ?>

    <?php // echo $form->field($model, 'datetime_finish') ?>

    <?php // echo $form->field($model, 'datetime_access') ?>

    <?php // echo $form->field($model, 'valid_datetime') ?>

    <?php // echo $form->field($model, 'id_route') ?>

    <?php // echo $form->field($model, 'id_route_real') ?>

    <?php // echo $form->field($model, 'id_price_zone_real') ?>

    <?php // echo $form->field($model, 'create_at') ?>

    <?php // echo $form->field($model, 'update_at') ?>

    <?php // echo $form->field($model, 'cost') ?>

    <?php // echo $form->field($model, 'type_payment') ?>

    <?php // echo $form->field($model, 'id_payment') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'paid_status') ?>

    <?php // echo $form->field($model, 'comment') ?>

    <?php // echo $form->field($model, 'id_vehicle') ?>

    <?php // echo $form->field($model, 'id_driver') ?>

    <?php // echo $form->field($model, 'FLAG_SEND_EMAIL_STATUS_EXPIRED') ?>

    <?php // echo $form->field($model, 'real_body_type') ?>

    <?php // echo $form->field($model, 'real_loading_typies') ?>

    <?php // echo $form->field($model, 'real_tonnage') ?>

    <?php // echo $form->field($model, 'real_length') ?>

    <?php // echo $form->field($model, 'real_height') ?>

    <?php // echo $form->field($model, 'real_width') ?>

    <?php // echo $form->field($model, 'real_volume') ?>

    <?php // echo $form->field($model, 'real_longlength') ?>

    <?php // echo $form->field($model, 'real_passengers') ?>

    <?php // echo $form->field($model, 'real_ep') ?>

    <?php // echo $form->field($model, 'real_rp') ?>

    <?php // echo $form->field($model, 'real_lp') ?>

    <?php // echo $form->field($model, 'real_tonnage_spec') ?>

    <?php // echo $form->field($model, 'real_length_spec') ?>

    <?php // echo $form->field($model, 'real_volume_spec') ?>

    <?php // echo $form->field($model, 'real_cargo') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
