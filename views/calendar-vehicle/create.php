<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CalendarVehicle */

$this->title = 'Create Calendar Vehicle';
$this->params['breadcrumbs'][] = ['label' => 'Calendar Vehicles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="calendar-vehicle-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
