<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\RequestPaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Выплаты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="request-payment-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Новый запрос', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'create_at',
            'cost',
            'type_payment',
            'status',
//            'requisites:ntext',
            //'url_files:ntext',
            //'status',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
