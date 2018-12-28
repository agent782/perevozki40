<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Order */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'id_user',
            'id_company',
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
            'datetime_start:datetime',
            'datetime_finish:datetime',
            'datetime_access:datetime',
            'valid_datetime:datetime',
            'id_route',
            'id_route_real',
            'id_price_zone_real',
            'create_at',
            'update_at',
            'cost',
            'type_payment',
            'id_payment',
            'status',
            'paid_status',
            'comment:ntext',
            'id_vehicle',
            'id_driver',
            'FLAG_SEND_EMAIL_STATUS_EXPIRED:email',
            'real_body_type',
            'real_loading_typies',
            'real_tonnage',
            'real_length',
            'real_height',
            'real_width',
            'real_volume',
            'real_longlength',
            'real_passengers',
            'real_ep',
            'real_rp',
            'real_lp',
            'real_tonnage_spec',
            'real_length_spec',
            'real_volume_spec',
            'real_cargo',
        ],
    ]) ?>

</div>
