<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Order */

$this->title = 'Заказ №' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="order-view">

    <h4><?= Html::encode($this->title) ?></h4>
    <div class="row">
        <div class="col-lg4">

        </div>

        <div class="col-lg4">

        </div>

        <div class="col-lg4">

        </div>
    </div>




    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'id_vehicle_type',
            'tonnage',
            'length',
            'width',
            'height',
            'volume',
            'longlength',
            'passengers',
            'ep',
            'rp',
            'lp',
            'tonnage_spec',
            'length_spec',
            'volume_spec',
            'cargo:ntext',
            'datetime_start',
            'datetime_finish',
            'datetime_access',
            'valid_datetime',
            'create_at',
            'id_route',
            'id_route_real',
            'type_payment'
        ],
    ]) ?>

</div>
