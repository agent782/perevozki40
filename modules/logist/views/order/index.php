<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'ЗАКАЗЫ';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">
    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Новый заказ', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <a class = "btn btn-link left"><h3>В поиске  ( + <?= $countNewOrders ?>)</h3></a>
    <div id="new_orders">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'id_user',
            'id_company',
            'id_vehicle_type',
            'tonnage',
            //'length',
            //'width',
            //'height',
            //'volume',
            //'longlength',
            //'passengers',
            //'ep',
            //'rp',
            //'lp',
            //'tonnage_spec',
            //'length_spec',
            //'volume_spec',
            //'cargo:ntext',
            //'datetime_start:datetime',
            //'datetime_finish:datetime',
            //'datetime_access:datetime',
            //'valid_datetime:datetime',
            //'id_route',
            //'id_route_real',
            //'id_price_zone_real',
            //'create_at',
            //'update_at',
            //'cost',
            //'type_payment',
            //'id_payment',
            //'status',
            //'paid_status',
            //'comment:ntext',
            //'id_vehicle',
            //'id_driver',
            //'FLAG_SEND_EMAIL_STATUS_EXPIRED:email',
            //'real_body_type',
            //'real_loading_typies',
            //'real_tonnage',
            //'real_length',
            //'real_height',
            //'real_width',
            //'real_volume',
            //'real_longlength',
            //'real_passengers',
            //'real_ep',
            //'real_rp',
            //'real_lp',
            //'real_tonnage_spec',
            //'real_length_spec',
            //'real_volume_spec',
            //'real_cargo',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    </div>
    <br>
    <a class = "btn btn-link left"><h3>В процессе</h3></a>
    <div id="in_process_orders">

    </div>
    <br>
    <a class = "btn btn-link left"><h3>Архив</h3></a>
    <div id="arhive_orders">

    </div>
</div>
