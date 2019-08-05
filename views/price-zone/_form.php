<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Vehicle;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model app\models\PriceZone */
/* @var $form yii\widgets\ActiveForm */

$body_typies= \app\models\BodyType::find()->where(['id_type_vehicle' => $model->veh_type])
    ->orderBy(['body' => SORT_ASC])
    ->all();
?>
<div class="price-zone-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'veh_type')->hiddenInput()->label(false) ?>

    <?= ($model->veh_type != Vehicle::TYPE_SPEC)? $form->field($model, 'body_types')->checkboxList(
        ArrayHelper::map(
        $body_typies, 'id', 'body')):
    $form->field($model, 'body_types')->radioList(
     ArrayHelper::map(
        $body_typies, 'id', 'body'));
    ?>
    <?= $form->field($model, 'longlength')->radioList(['Нет', 'Да']) ?>

    <?= $form->field($model, 'tonnage_min')->textInput() ?>

    <?= $form->field($model, 'tonnage_max')->textInput() ?>

    <?= $form->field($model, 'volume_min')->textInput() ?>

    <?= $form->field($model, 'volume_max')->textInput() ?>

    <?= $form->field($model, 'length_min')->textInput() ?>

    <?= $form->field($model, 'length_max')->textInput() ?>

    <?= $form->field($model, 'passengers')->textInput() ?>

    <?= $form->field($model, 'tonnage_spec_min')->textInput() ?>

    <?= $form->field($model, 'tonnage_spec_max')->textInput() ?>

    <?= $form->field($model, 'length_spec_min')->textInput() ?>

    <?= $form->field($model, 'length_spec_max')->textInput() ?>

    <?= $form->field($model, 'volume_spec')->textInput() ?>

    <?= $form->field($model, 'r_km')->textInput() ?>

    <?= $form->field($model, 'h_loading')->textInput() ?>

    <?= $form->field($model, 'r_loading')->textInput() ?>

    <?= $form->field($model, 'min_price')->textInput() ?>

    <?= $form->field($model, 'r_h')->textInput() ?>

    <?= $form->field($model, 'min_r_10')->textInput() ?>

    <?= $form->field($model, 'min_r_20')->textInput() ?>

    <?= $form->field($model, 'min_r_30')->textInput() ?>

    <?= $form->field($model, 'min_r_40')->textInput() ?>

    <?= $form->field($model, 'min_r_50')->textInput() ?>

    <?= $form->field($model, 'remove_awning')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
