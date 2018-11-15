<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Driver */

$this->title = 'Редактирование: ' . $model->surname . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Drivers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="driver-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'DriverForm' => $DriverForm
    ]) ?>

</div>
