<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\components\widgets\ShowMessageWidget;
use app\models\PriceZone;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PriceZoneSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Тарифные зоны';
$this->params['breadcrumbs'][] = $this->title;

$actionColumn = [];
$addButton = '';
if(Yii::$app->user->can('admin')){
    $addButton = Html::a('Добавить тариф', ['create'], ['class' => 'btn btn-success']);
    $actionColumn = ['class' => 'yii\grid\ActionColumn'];
}
?>
<div class="price-zone-index">

    <h4><?= Html::encode($this->title) ?></h4>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Рассчитать стоимость', '/order/create', ['class' => 'btn btn-primary'])?>
        <?= $addButton ?>
    </p>
    <h4 >Грузовой транспорт</h4>
    <div>
    <?= GridView::widget([
        'dataProvider' => $dataProviderTruck,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'options' => [
            'style' => 'font-size: 10px'
        ],
        'columns' => [
            'id',
            [
                'attribute' => 'bodyTypiesText',
                'label' => 'Типы кузова',
                'format' => 'raw',
                'value' => function(PriceZone $model){
                    if($model->hasAllBodyTypies()) {
                        return 'Любой'
//                            . ShowMessageWidget::widget([
//                                'helpMessage' => $model->bodiesColumn
//                            ])
                            ;
                    } else {
                        return $model->bodyTypiesText
                            . ' '
                            . ShowMessageWidget::widget([
                                'helpMessage' => $model->bodiesColumn
                            ]);
                    }
                }
            ],
            [
                'attribute' => 'longlength',
                'label' => 'Груз-длинномер',
                'format' => 'raw',
                'value' => function($model){
                    return ($model->longlength)?
                        \yii\bootstrap\Html::img('/img/icons/yes-20.png', ['title' => 'Да']):
                        \yii\bootstrap\Html::img('/img/icons/no-20.png', ['title' => 'Нет'])
                    ;
                }
            ],
            [
                'label' => 'Тоннаж (т.)',
                'format' => 'raw',
                'value' => function($model){
                    return ($model->tonnage_max)?
                        $model->tonnage_min
                        . ' - '
                        . $model->tonnage_max
//                        . ' т.'
                        :
                        '---'
                        ;
                }
            ],
            [
                'label' => 'Длина кузова (м.)',
                'format' => 'raw',
                'value' => function(PriceZone $model){
                    $return = '';
                    if($model->length_max){
                        $return .= $model->length_min
                            . ' - '
                            . $model->length_max;
//                          . ' м.';
                        if($model->longlength){
                            $return .= ' *';
                        }
                    } else {
                        $return .= '---';
                    }
                    return $return;
                }
            ],
            [
                'label' => 'Объем кузова (м3)',
                'format' => 'raw',
                'value' => function($model){
                    return ($model->volume_max)?
                        $model->volume_min
                        . ' - '
                        . $model->volume_max
//                        . ' м3.'
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

            $actionColumn        ],
    ]); ?>


    <h4>Пассажирский транспорт</h4>
    <?= GridView::widget([
        'dataProvider' => $dataProviderPass,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'options' => [
            'style' => 'font-size: 10px'
        ],
        'columns' => [
            'id',
            [
                'attribute' => 'bodyTypiesText',
                'label' => 'Типы кузова',
                'format' => 'raw',
                'value' => function(\app\models\PriceZone $model){
                    if($model->hasAllBodyTypies()) {
                        return 'Любой'
//                            . ShowMessageWidget::widget([
//                                'helpMessage' => $model->bodiesColumn
//                            ])
                            ;
                    } else {
                        return $model->bodyTypiesText
                            . ' '
                            . ShowMessageWidget::widget([
                                'helpMessage' => $model->bodiesColumn
                            ]);
                    }
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
//            'remove_awning',
            $actionColumn        ],
    ]); ?>

    <h4>Спецтехника</h4>
    <?= GridView::widget([
        'dataProvider' => $dataProviderSpec,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'options' => [
            'style' => 'font-size: 10px'
        ],
        'columns' => [
            'id',
            [
                'attribute' => 'bodyTypiesText',
                'label' => 'Типы кузова',
                'format' => 'raw',
            ],
            [
                'label' => 'Тоннаж (т.)',
                'format' => 'raw',
                'value' => function($model){
                    return ($model->tonnage_max)?
                        $model->tonnage_min
                        . ' - '
                        . $model->tonnage_max
                        :
                        '---'
                        ;
                }
            ],
            [
                'label' => 'Длина кузова (м.)',
                'format' => 'raw',
                'value' => function($model){
                    return ($model->length_max)?
                        $model->length_min
                        . ' - '
                        . $model->length_max
                        :
                        '---'
                        ;
                }
            ],
            [
                'label' => 'Объем кузова (м3)',
                'format' => 'raw',
                'value' => function($model){
                    return ($model->volume_max)?
                        $model->volume_min
                        . ' - '
                        . $model->volume_max
                        :
                        '---'
                        ;
                }
            ],
            [
                'attribute' => 'tonnage_spec_max',
                'label' => 'Грузоподъемность механизма (стрелы) (т.)',
                'format' => 'raw',
                'value' => function($model){
                    return ($model->tonnage_spec_max)?
                        $model->tonnage_spec_min
                        . ' - '
                        . $model->tonnage_spec_max
                        :
                        '---'
                        ;
                }
            ],
            [
                'attribute' => 'lengrh_spec_max',
                'label' => 'Длина механизма (стрелы) (м.)',
                'format' => 'raw',
                'value' => function($model){
                    return ($model->length_spec_max)?
                        $model->length_spec_min
                        . ' - '
                        . $model->length_spec_max
                        :
                        '---'
                        ;
                }
            ],
            [
                'attribute' => 'volume_spec_max',
                'label' => 'Объем механизма (ковша) (м3)',
                'format' => 'raw',
                'value' => function($model){
                    return ($model->volume_spec)?
                        $model->volume_spec
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
            $actionColumn
        ],
    ]); ?>
    </div>
    <comment>* В тарифах для груза-длинномера длина указана
        с учетом допустимого выступания груза за габариты кузова на 2 метра.</comment>
</div>
