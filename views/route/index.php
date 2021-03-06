<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\RouteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Routes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="route-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Route', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'startCity',
            'finishCity',
            'routeStart',
            'route1',
            //'route2',
            //'route3',
            //'route4',
            //'route5',
            //'route6',
            //'route7',
            //'route8',
            //'routeFinish',
            //'distance',
            //'count',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
