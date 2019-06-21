<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\VehicleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'База ТС';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vehicle-index">

    <h2><?= Html::encode($this->title) ?></h2>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить ТС', [
            '/user/find-user',
            'redirect' => \yii\helpers\Url::to(['/vehicle/create']),
            'redirect2' => '/logist/vehicle'],
            ['class' => 'btn btn-success'
            ])
        ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'id_user',
            'id_vehicle_type',
            'body_type',
            'tonnage',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
