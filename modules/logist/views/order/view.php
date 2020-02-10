<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Order */

//$this->title = $model->id;
//$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
    $this->title = 'Заказ №' . $model->id;
?>
<div class="container order-view">
    <h4><?= Html::encode($this->title) ?></h4>
    <div class="row">
        <div class="col-lg-4">
            Для водитеоля
            <?= $model->PrintFinishCalculate(true, true)?>
        </div>
        <div class="col-lg-4">
            Для клиента
            <?= $model->PrintFinishCalculate(true, false, true)?>
        </div>
        <div class="col-lg-4">
            <?= $model->getFullFinishInfo(true, null, true, true)?>
        </div>
    </div>

</div>
