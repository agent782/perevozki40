<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Order */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'id_company')->textInput() ?>

    <?= $form->field($model, 'id_vehicle_type')->textInput() ?>

    <?= $form->field($model, 'tonnage')->textInput() ?>

    <?= $form->field($model, 'length')->textInput() ?>

    <?= $form->field($model, 'width')->textInput() ?>

    <?= $form->field($model, 'height')->textInput() ?>

    <?= $form->field($model, 'volume')->textInput() ?>

    <?= $form->field($model, 'longlength')->textInput() ?>

    <?= $form->field($model, 'passengers')->textInput() ?>

    <?= $form->field($model, 'ep')->textInput() ?>

    <?= $form->field($model, 'rp')->textInput() ?>

    <?= $form->field($model, 'lp')->textInput() ?>

    <?= $form->field($model, 'tonnage_spec')->textInput() ?>

    <?= $form->field($model, 'length_spec')->textInput() ?>

    <?= $form->field($model, 'volume_spec')->textInput() ?>

    <?= $form->field($model, 'cargo')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'datetime_start')->textInput() ?>

    <?= $form->field($model, 'datetime_finish')->textInput() ?>

    <?= $form->field($model, 'datetime_access')->textInput() ?>

    <?= $form->field($model, 'valid_datetime')->textInput() ?>

    <?= $form->field($model, 'id_route')->textInput() ?>

    <?= $form->field($model, 'id_route_real')->textInput() ?>

    <?= $form->field($model, 'id_price_zone_real')->textInput() ?>

    <?= $form->field($model, 'create_at')->textInput() ?>

    <?= $form->field($model, 'update_at')->textInput() ?>

    <?= $form->field($model, 'cost')->textInput() ?>

    <?= $form->field($model, 'type_payment')->textInput() ?>

    <?= $form->field($model, 'id_payment')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'paid_status')->textInput() ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'id_vehicle')->textInput() ?>

    <?= $form->field($model, 'FLAG_SEND_EMAIL_STATUS_EXPIRED')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
