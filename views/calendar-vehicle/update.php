<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CalendarVehicle */

$this->title = 'Update Calendar Vehicle: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Calendar Vehicles', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="calendar-vehicle-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
