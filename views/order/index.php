<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Order', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'id_vehicle_type',
            'tonnage',
//            'length',
//            'width',
            // 'height',
            // 'volume',
            // 'longlength',
            // 'passengers',
            // 'ep',
            // 'rp',
            // 'lp',
            // 'tonnage_spec',
            // 'length_spec',
            // 'volume_spec',
            // 'cargo:ntext',
             'datetime_start',
            // 'datetime_finish:datetime',
            // 'datetime_access:datetime',
             'valid_datetime',
            'create_at',
            // 'id_route',
            // 'id_route_real',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
