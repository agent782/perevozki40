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
    <h4>В процессе выполнения...</h4>
    <?= GridView::widget([
        'dataProvider' => $dataProvider_in_process,
//    'filterModel' => $searchModel,
//        'bordered' => true,
//        'striped' => false,
//        'responsive'=>true,
//        'floatHeader'=>false,
        'options' => [
            'class' => 'minRoute'
        ],
//        'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
//        'headerRowOptions'=>['class'=>'kartik-sheet-style'],
//        'filterRowOptions'=>['class'=>'kartik-sheet-style'],
//        'persistResize'=>true,
        'responsiveWrap' => false,
        'pjax'=>true,
        'columns' => [
//            [
//                'class' => 'kartik\grid\ExpandRowColumn',
//                'value' => function ($model, $key, $index, $column) {
//
//                    return GridView::ROW_COLLAPSED;
//                },
//                'enableRowClick' => true,
//                'allowBatchToggle'=>true,
//                'detail'=>function ($model) {
////                    return $model->id;
//                    return Yii::$app->controller->renderPartial('view', ['model'=>$model]);
//                },
//                'detailOptions'=>[
//                    'class'=> 'kv-state-enable',
//                ],
//            ],
            'id',
            'datetime_start',
            [
                'label' => 'ТС',
                'format' => 'raw',
                'value' => function($order){
                    return $order->getFullInfoAboutVehicle(true,true,true,true);
                }
            ],
            [
            'label' => 'Маршрут',
            'format' => 'raw',
            'attribute' => 'route.fullRoute'
            ],
            [
                'label' => 'Информация о заказе',
                'format' => 'raw',
                'attribute'=>'shortInfoForClient'
            ],
            [
                'label' => 'Тариф',
                'format' => 'raw',
                'attribute' => 'id_pricezone_for_vehicle',
                'value' => function($modelOrder){
                    return \app\models\PriceZone::findOne($modelOrder
                        ->id_pricezone_for_vehicle)
                        ->getTextWithShowMessageButton($modelOrder->route->distance, true, $modelOrder->discount);
                }
            ],
            [
                'label' => 'Заказчик',
                'format' => 'raw',
                'attribute' => 'clientInfo'
            ],
            [
                'attribute' => 'paymentText',
                'format' => 'raw'
            ],
            [
                'label' => 'Действия',
                'format' => 'raw',
                'value' => function($model){
                    return
                        Html::a(Html::icon('remove', ['class' => 'btn-lg','title' => 'Отменить заказ']), Url::to([
                            '/order/canceled-by-client',
                            'id_order' => $model->id,
                            'id_vehicle' => $model->id_vehicle
                        ]),
                            ['data-confirm' => Yii::t('yii',
                                'Заказ в процессе выполнения! 
                                Пожалуйста, перед нажатием кнопки "ОК" позвоните водителю, принявшему Ваш заказ и предупредите об отмене.<br><br> Водитель: ' .
                                $model->vehicleFioAndPhone)
                                . '<br><br><i> Водитель имеет возможность оценить корректность Ваших действий, что может повлиять на Ваш рейтинг Клиента.</i>',
                                'data-method' => 'post'])                    ;
                }

            ],
        ]
    ]); ?>
</div>