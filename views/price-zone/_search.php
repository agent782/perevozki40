<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PriceZoneSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="price-zone-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'veh_type') ?>

    <?= $form->field($model, 'body_types') ?>

    <?= $form->field($model, 'longlenth') ?>

    <?= $form->field($model, 'tonnage_min') ?>

    <?php // echo $form->field($model, 'tonnage_max') ?>

    <?php // echo $form->field($model, 'volume_min') ?>

    <?php // echo $form->field($model, 'volume_max') ?>

    <?php // echo $form->field($model, 'length_min') ?>

    <?php // echo $form->field($model, 'length_max') ?>

    <?php // echo $form->field($model, 'tonnage_long_min') ?>

    <?php // echo $form->field($model, 'tonnage_long_max') ?>

    <?php // echo $form->field($model, 'length_long_min') ?>

    <?php // echo $form->field($model, 'length_long_max') ?>

    <?php // echo $form->field($model, 'passengers') ?>

    <?php // echo $form->field($model, 'tonnage_spec_min') ?>

    <?php // echo $form->field($model, 'tonnage_spec_max') ?>

    <?php // echo $form->field($model, 'length_spec') ?>

    <?php // echo $form->field($model, 'volume_spec') ?>

    <?php // echo $form->field($model, 'r_km') ?>

    <?php // echo $form->field($model, 'h_loading') ?>

    <?php // echo $form->field($model, 'r_loading') ?>

    <?php // echo $form->field($model, 'min_price') ?>

    <?php // echo $form->field($model, 'r_h') ?>

    <?php // echo $form->field($model, 'min_r_10') ?>

    <?php // echo $form->field($model, 'min_r_20') ?>

    <?php // echo $form->field($model, 'min_r_30') ?>

    <?php // echo $form->field($model, 'min_r_40') ?>

    <?php // echo $form->field($model, 'min_r_50') ?>

    <?php // echo $form->field($model, 'remove_awning') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
