<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PriceZone */

$this->title = 'Update Price Zone: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Price Zones', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="price-zone-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
