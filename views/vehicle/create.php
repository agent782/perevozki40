<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Html;
use app\models\VehicleType;


/* @var $this yii\web\View */
/* @var $model app\models\Vehicle */

$this->title = 'Новое транспортное средство.';
//$this->params['breadcrumbs'][] = ['label' => 'Vehicles', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
?>

<div class="vehicle-create">

    <h4><?= Html::encode($this->title) ?></h4>
<br>
    <?php
    $form = ActiveForm::begin([

    ]);
//    var_dump($VehicleForm);

    $vehTypes = ArrayHelper::map(VehicleType::find()->asArray()->all(), 'id', 'type');
    ?>
    <div class="row">
        <div class="col-lg-4">
            <br>
            <?= $form->field($VehicleForm, 'vehicleTypeId')->radioList($vehTypes)?>
        </div>
        <div class="col-lg-4">

        </div>
        <div class="col-lg-4">

        </div>
    </div>
    <div class="col-lg-12 form-group">
        <?= Html::a('Назад', \yii\helpers\Url::to('/vehicle'), ['class' => 'btn btn-warning'])?>
        <?= Html::submitButton('Далее', ['class' => 'btn btn-success', 'name' => 'button', 'value' => 'create_next1']) ?>


    </div>

    <?php ActiveForm::end(); ?>

</div>
