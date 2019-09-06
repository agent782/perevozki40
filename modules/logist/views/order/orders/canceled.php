<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 28.02.2019
 * Time: 13:13
 */
use kartik\grid\GridView;
use app\models\Order;
use yii\bootstrap\Html;
use app\models\Vehicle;
use yii\helpers\Url;
use yii\bootstrap\Tabs;
?>
<div>
    <h4>Отмененные и просроченные.</h4>
    <?= GridView::widget([
        'dataProvider' => $dataProvider_canceled,
//        'filterModel' => $searchModel,
        'options' => [
            'class' => 'minRoute'
        ],
        'responsiveWrap' => false,
        'pjax' => true,
        'pjax'=>true,'pjaxSettings' => [
            'options' => [
                'id' => 'pjax_canceled_orders'
            ]
        ],
        'columns' => [
            [
                'class' => 'kartik\grid\ExpandRowColumn',
                'value' => function ($model, $key, $index, $column) {

                    return GridView::ROW_COLLAPSED;
                },
                'enableRowClick' => true,
                'allowBatchToggle'=>true,
                'detail'=>function ($model) {
//                    return $model->id;
                    return Yii::$app->controller->renderPartial('view', ['model'=>$model]);
                },
                'detailOptions'=>[
                    'class'=> 'kv-state-enable',
                ],
            ],
            'id',
            'statusText',
            [
                'attribute' => 'datetime_start',
                'options' => [
//                    'style' =>'width: 100px',
                ],
                'contentOptions'=>['style'=>'white-space: normal;']
            ],
            [
                'label' => 'Маршрут',
                'format' => 'raw',
                'attribute' => 'route.fullRoute'
            ],
            [
                'label' => 'Информация',
                'format' => 'raw',
                'attribute'=>'shortInfoForClient',
                'value' => function(Order $order){
                    return $order->getShortInfoForClient(true);
                }
            ],
            [
                'label' => 'Заказчик',
                'format' => 'raw',
                'attribute' => 'clientInfo'
            ],
            [
                'label' => 'Тарифные зоны',
                'format' => 'raw',
                'value' => function($model){
                    return $model->getListPriceZonesCostsWithDiscont($model->route->distance, $model->getDiscount($model->id_user));
                }            ],
            [
                'attribute' => 'paymentText',
                'format' => 'raw'
            ],
            'valid_datetime',
            [
                'label' => '',
                'format' => 'raw',
                'value' => function($model){
                    if($model->status == Order::STATUS_CANCELED
                        || $model->status == Order::STATUS_EXPIRED){

                        return Html::a('Изменить и повторить поиск', Url::to([
                            '/order/update',
                            'id_order' => $model->id,
                            'redirect' => '/logist/order'
                        ]),
                            [
                                'data-method' => 'post',
                                'class' => 'btn btn-primary']);
                    }
                }
            ],
        ],
    ]); ?>
</div>