<?php
    $VehicleForm = new \app\models\VehicleForm();
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\VehicleType;

/* @var $this yii\web\View */
/* @var $model app\models\Vehicle */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="vehicle-form">
    <?php
        $form = ActiveForm::begin([

        ]);
         ?>
    <div class="row">
        <div class="col-lg-4">

        </div>
        <div class="col-lg-4">

        </div>
        <div class="col-lg-4">

        </div>
    </div>
    <div class="col-lg-12 form-group">
        <?= Html::submitButton($modelVehicle->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $modelVehicle->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a('Отменить', \yii\helpers\Url::previous(), ['class' => 'btn btn-warning'])?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
