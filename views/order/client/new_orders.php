<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 05.01.2019
 * Time: 12:28
 */
use kartik\grid\GridView;
use app\models\Order;
use yii\bootstrap\Html;
use app\models\Vehicle;
use yii\helpers\Url;
use yii\bootstrap\Tabs;
?>
<div>
    <h4>В процессе поиска ТС...</h4>
<?= GridView::widget([
    'dataProvider' => $dataProvider_newOrders,
    'options' => [
        'class' => 'minRoute'
    ],
    'responsiveWrap' => false,
    'pjax'=>true,
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
            'attribute'=>'shortInfoForClient'
        ],
        [
            'label' => 'Заказчик',
            'format' => 'raw',
            'attribute' => 'clientInfo'
        ],
        [
            'label' => 'Тарифные зоны',
            'format' => 'raw',
            'attribute' => 'idsPriceZonesWithPriceAndShortInfo'
        ],
        'paymentText',
        'valid_datetime',
        [
            'label' => 'Действия',
            'format' => 'raw',
            'value' => function($model){
                if($model->status == Order::STATUS_NEW || $model->status == Order::STATUS_IN_PROCCESSING){
                    return
                        Html::a(Html::icon('edit', ['class' => 'btn-lg','title' => 'Изменить заказ']), [
                                '/order/update',
                                'id_order' => $model->id,
                                'redirect' => '/order/client'
                            ])
                        . ' '
                        . Html::a(Html::icon('remove', ['class' => 'btn-lg','title' => 'Отменить заказ']), Url::to([
                            '/order/canceled-by-client',
                            'id_order' => $model->id,
                        ]),
                            ['data-confirm' => Yii::t('yii',
                                'Пока заказ не принят водителем, Вы можете отменить его без потери рейтинга. Отменить заказ?'),
                                'data-method' => 'post'])
                        ;

                }
            }
        ]
    ],
]); ?>
</div>