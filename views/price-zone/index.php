<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PriceZoneSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Тарифы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="price-zone-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить тариф', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <h2>Грузовой транспорт</h2>
    <div class="col-lg-12">
    <?= GridView::widget([
        'dataProvider' => $dataProviderTruck,
        'filterModel' => $searchModel,
        'options' => [
                'class' => 'wrap'
//            'style' => 'vertical-align: top'
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'attribute' => 'bodiesColumn',
                'format' => 'html',
            ],
            [
                'attribute' => 'longLength',
                'label' => 'Груз-длинномер',
                'format' => 'raw',
                'value' => function($model){
                    return ($model->longlength)?
                        $model->length_min
                        . ' - '
                        . $model->length_max
                        . ' м.'
                        . '<br>'
                        . $model->tonnage_min
                        . ' - '
                        . $model->tonnage_max
                        . ' т.'
                        :
                        '---'
                        ;
                }
            ],
            [
                'attribute' => 'tonnage_max',
                'label' => 'Тоннаж',
                'format' => 'raw',
                'value' => function($model){
                    return ($model->tonnage_max)?
                        $model->tonnage_min
                        . ' - '
                        . $model->tonnage_max
                        . ' т.'
                        :
                        '---'
                        ;
                }
            ],
            [
                'attribute' => 'length_max',
                'label' => 'Длинна кузова',
                'format' => 'raw',
                'value' => function($model){
                    return ($model->length_max)?
                        $model->length_min
                        . ' - '
                        . $model->length_max
                        . ' м.'
                        :
                        '---'
                        ;
                }
            ],
            [
                'attribute' => 'volume_max',
                'label' => 'Объем кузова',
                'format' => 'raw',
                'value' => function($model){
                    return ($model->volume_max)?
                        $model->volume_min
                        . ' - '
                        . $model->volume_max
                        . ' м3.'
                        :
                        '---'
                        ;
                }
            ],
             'passengers',

             'r_km',
             'h_loading',
             'r_loading',
             'min_price',
             'r_h',
             'min_r_10',
             'min_r_20',
             'min_r_30',
             'min_r_40',
             'min_r_50',
             'remove_awning',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


    <h2>Пассажирский транспорт</h2>
    <?= GridView::widget([
        'dataProvider' => $dataProviderPass,
        'filterModel' => $searchModel,
        'options' => [
                'class' => 'wrap',
//            'style' => 'vertical-align: top'
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'bodiesColumn',
                'format' => 'html',
            ],
            'id',
            'passengers',
            'r_km',
            'h_loading',
            'r_loading',
            'min_price',
            'r_h',
            'min_r_10',
            'min_r_20',
            'min_r_30',
            'min_r_40',
            'min_r_50',
//            'remove_awning',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <h2>Спецтехника</h2>
    <?= GridView::widget([
        'dataProvider' => $dataProviderSpec,
        'filterModel' => $searchModel,
        'options' => [
            'style' => 'vertical-align: top'
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'attribute' => 'bodiesColumn',
                'format' => 'html',
            ],
            'tonnage_spec_min',
            'tonnage_spec_max',
            'length_spec_min',
            'length_spec_max',
            'volume_spec',
            [
                'attribute' => 'tonnage_max',
                'label' => 'Тоннаж',
                'format' => 'raw',
                'value' => function($model){
                    return ($model->tonnage_max)?
                        $model->tonnage_min
                        . ' - '
                        . $model->tonnage_max
                        . ' т.'
                        :
                        '---'
                        ;
                }
            ],
            [
                'attribute' => 'length_max',
                'label' => 'Длинна кузова',
                'format' => 'raw',
                'value' => function($model){
                    return ($model->length_max)?
                        $model->length_min
                        . ' - '
                        . $model->length_max
                        . ' м.'
                        :
                        '---'
                        ;
                }
            ],
            [
                'attribute' => 'volume_max',
                'label' => 'Объем кузова',
                'format' => 'raw',
                'value' => function($model){
                    return ($model->volume_max)?
                        $model->volume_min
                        . ' - '
                        . $model->volume_max
                        . ' м3.'
                        :
                        '---'
                        ;
                }
            ],
            'r_km',
            'h_loading',
            'r_loading',
            'min_price',
            'r_h',
            'min_r_10',
            'min_r_20',
            'min_r_30',
            'min_r_40',
            'min_r_50',
            'remove_awning',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    </div>
</div>
