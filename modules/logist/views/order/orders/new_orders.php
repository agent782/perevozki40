<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 05.01.2019
 * Time: 12:28
 */
/* @var Order $model*/
use kartik\grid\GridView;
use app\models\Order;
use yii\bootstrap\Html;
use app\models\Vehicle;
use yii\helpers\Url;
use yii\bootstrap\Tabs;
use \app\models\XprofileXcompany;
use yii\web\JsExpression;
use kartik\grid\EditableColumn;
use kartik\editable\Editable;

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
    'pjaxSettings' => [
        'options' => [
            'id' => 'pjax_new_orders'
        ]
    ],
    'columns' => [
//        [
//            'class' => 'kartik\grid\ExpandRowColumn',
//            'value' => function ($model, $key, $index, $column) {
//
//                return GridView::ROW_COLLAPSED;
//            },
//            'enableRowClick' => true,
//            'allowBatchToggle'=>true,
//            'detail'=>function ($model) {
////                    return $model->id;
//                return Yii::$app->controller->renderPartial('view', ['model'=>$model]);
//            },
//            'detailOptions'=>[
//                'class'=> 'kv-state-enable',
//            ],
//        ],
        [
            'attribute' => 'id',
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'h5'
            ]
        ],
        [
            'attribute' => 'hide',
            'format' => 'raw',
            'value' => function (Order $model, $index){
                return Html::checkbox('hide', $model->hide, [
                    'onchange' => new JsExpression('
                        $.ajax({
                        url: "/logist/order/hide",
                        type: "POST",
                        dataType: "json",
                        data: {
                            id_order : '. $index .'
                        },
                        
                        success: function(data){
                            if(data){
                                $(this).attr("checked", true);
                            } else {
                                $(this).attr("checked", false);
                            }
                        },
                        error: function(){
                            alert("Ошибка на сервере %(");
                        }
                    });    
                    ')
                ]);
            }
        ],
        [
            'label' => 'Дата/время',
            'format' => 'raw',
            'value' => function($model){
                return
                    $model->datetime_start
                    . '<br><i>('
                    . $model->valid_datetime
                    . ')</i>'
                    ;
            },
            'contentOptions' => [
                'class' => 'h5'
            ]
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
            'value' => function (Order $model){
                return $model->getShortInfoForClient(true);
            }
        ],
        [
            'label' => 'Заказчик',
            'format' => 'raw',
//            'attribute' => 'clientInfo',
            'value' => function (Order $model){
                $return = $model->getClientInfo();
                $company = \app\models\Company::findOne($model->id_company);
                if(!$company){

                        $return .= '<br>' . Html::a(Html::icon('plus', ['title' => 'Добавить юр. лицо', 'class' => 'btn-xs btn-primary']),
                                ['/logist/order/add-company', 'id_order' => $model->id]);
                }
                return $return;
            },
            'contentOptions' => [
                'style' => 'font-size: 14px'
            ]
        ],
        [
            'label' => 'Выбранные тарифы для водителя',
            'format' => 'raw',
            'value' => function($model){
                return $model->getListPriceZonesCostsForVehicle(null, $model->route->distance);
//                return $model->getListPriceZonesCostsWithDiscont($model->route->distance, $model->getVehicleProcentPrice(), false);
            }
        ],
        [
            'label' => 'Выбранные тарифы для Клиента',
            'format' => 'raw',
            'value' => function($model){
                return $model->getListPriceZonesCostsWithDiscont($model->route->distance, $model->discount);
            }
        ],
        [
            'attribute' => 'paymentText',
            'format' => 'raw'
        ],
        [
            'label' => 'Поиск...',
            'format' => 'raw',
            'value' => function(Order $model){
                return count($model->getMessagesNewOrder());
            }
        ],
        [
            'label' => 'Действия',
            'format' => 'raw',
            'value' => function($model){
                if($model->status == Order::STATUS_NEW || $model->status == Order::STATUS_IN_PROCCESSING){
                    $return =
                        Html::a(Html::icon('ok-sign', ['class' => 'btn-lg','title' => 'Назначить машину']), Url::to([
                            '/logist/order/find-vehicle',
                            'redirect' => '/order/accept-order',
                            'id_order' => $model->id,
                            'redirectError' => '/logist/order'
                        ]))
                        . ' '
                        . Html::a(Html::icon('edit', ['class' => 'btn-lg','title' => 'Изменить заказ']), [
                                '/order/update',
                                'id_order' => $model->id,
                                'redirect' => '/logist/order'
                            ])
                        . ' '
                        . Html::a(Html::icon('remove', ['class' => 'btn-lg','title' => 'Отменить заказ']), Url::to([
                            '/order/canceled-by-client',
                            'id_order' => $model->id,
                            'redirect' => '/logist/order'
                        ]),
                            ['data-confirm' => Yii::t('yii',
                                'Пока заказ не принят водителем, Вы можете отменить его без потери рейтинга. Отменить заказ?'),
                                'data-method' => 'post']);
                    if(Yii::$app->user->can('admin')) {
                        $hidden = ($model->auto_find)?true:false;
                        $return .= Html::a(Html::icon('search', ['class' => 'btn-lg',
                            'title' => 'Автоматический поиск']), Url::to([
                            '/order/auto-find',
                            'id_order' => $model->id,
//                            'redirect' => '/logist/order'
                        ]),
                            [
                                'target'=>'_blank',
                                'data-pjax' => '0',
//                                'data-confirm' => Yii::t('yii',
//                                    'Начать автоматический поиск ТС по заказу?'),
//                                'data-method' => 'post',
                                'id' => 'start_auto_find',
                                'hidden' => $hidden,
//                                'onclick' => new JsExpression('
//                                    $("#start_auto_find").prop("hidden", true);
//                                    $("#stop_auto_find").prop("hidden", false);
//                                    $.ajax({
//                                        url: "/logist/order/ajax-auto-find",
//                                        type: "POST",
//                                        dataType: "json",
//                                        data: {
//                                            id_order: ' . $model->id . ',
//                                        },
//
//                                        success: function(data){
//                                            if(data){
//                                                $("#start_auto_find").prop("hidden", false);
//                                                $("#stop_auto_find").prop("hidden", true);
//                                            }
//                                        },
//                                        error: function(){
//                                            alert("Ошибка на сервере!")
//                                        }
//                                     });
//                                ')
                            ]);
                        $return .= Html::a(Html::icon('pause', ['class' => 'btn-lg',
                            'title' => 'Автоматический поиск']), Url::to([
                            '#',
                        ]),
                            [
                                'hidden' => !$hidden,
                                'id' => 'stop_auto_find',
                                'onclick' => new JsExpression('
                                    $("#start_auto_find").prop("hidden", false);
                                    $("#stop_auto_find").prop("hidden", true);
                                    $.ajax({
                                        url: "/logist/order/ajax-stop-auto-find",
                                        type: "POST",
                                        dataType: "json",
                                        data: {
                                            id_order: ' . $model->id . ',
                                        },
                                        
                                        success: function(data){
                                            if(data){
                                                $("#start_auto_find").prop("hidden", false);
                                                $("#stop_auto_find").prop("hidden", true);
                                            }
                                        },
                                        error: function(){
                                            alert("Ошибка на сервере!")
                                        }
                                     });
                                ')
                            ]);
                    }
                    return $return;
                }
            }
        ]
    ],
]); ?>
</div>
