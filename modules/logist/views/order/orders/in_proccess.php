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
use app\components\widgets\FinishOrderOnlySumWidget;
use app\models\Company;
use app\components\widgets\ShowMessageWidget;
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
        'pjaxSettings' => [
            'options' => [
                'id' => 'pjax_in_proccess_orders'
            ]
        ],
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
                'attribute' => 'fullInfoAboutVehicle',
                'value' => function(Order $model){
                    $car_owner = $model->carOwner;
                    return ShowMessageWidget::widget([
                        'ToggleButton' => [
                            'label' => ($car_owner->old_id)? $car_owner->old_id : '#' . $car_owner->id_user
                        ],
                        'helpMessage' =>  $model->fullInfoAboutVehicle
                    ]);
                },
                'contentOptions' => [
                    'style' => 'font-size: 16px'
                ]
            ],
            [
            'label' => 'Маршрут',
            'format' => 'raw',
            'attribute' => 'route.fullRoute'
            ],
            [
                'label' => 'Информация о заказе',
                'format' => 'raw',
                'attribute'=>'shortInfoForClient',
                'value' => function(Order $order){
                    return $order->getShortInfoForClient(true);
                }
            ],
            [
                'label' => 'Тариф для Клиента',
                'format' => 'raw',
                'attribute' => 'id_pricezone_for_vehicle',
                'value' => function($modelOrder){
                    return \app\models\PriceZone::findOne(['unique_index' => $modelOrder
                        ->id_pricezone_for_vehicle])
                        ->getTextWithShowMessageButton($modelOrder->route->distance, true, $modelOrder->discount);
                }
            ],
            [
                'label' => 'Тариф для водителя',
                'format' => 'raw',
                'attribute' => 'id_pricezone_for_vehicle',
                'value' => function($modelOrder){
                    return \app\models\PriceZone::findOne(['unique_index' => $modelOrder
                        ->id_pricezone_for_vehicle])
                        ->getWithDiscount(\app\models\setting\SettingVehicle::find()->limit(1)->one()
                            ->price_for_vehicle_procent)
                        ->getTextWithShowMessageButton($modelOrder->route->distance, true);
                }
            ],
            [
                'label' => 'Заказчик',
                'format' => 'raw',
//            'attribute' => 'clientInfo',
                'value' => function ($model){
                    $return = '';
                    $company = Company::findOne($model->id_company);
                    if($model->id_user == $model->id_car_owner && $model->re){
                        $return = $model->comment;
                        if($company) {
                            $return .= '<br>'
                            . ($company->name_short) ? $company->name_short : $company->name;
                        }
                    } else {
                        $return  = $model->getClientInfo();
                    }
                    $re = ($model->re)? Html::icon('star') . '"авто"':'';
                    $return  = $re . '<br>' . $return;
//                    if(!$company){
                        $return .= '<br>'
                            . Html::a(Html::icon('edit',
                                ['title' => 'Добавить юр. лицо', 'class' => 'btn-xs btn-primary']
                            ),
                                ['/logist/order/add-company', 'id_order' => $model->id]);
//                    }
                    return $return;
                }
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
                        Html::a('Заказ выполнен', Url::to([
                                '/order/finish-by-vehicle',
                                'id_order' => $model->id,
                                'redirect' => '/logist/order'
                            ]),['class' => 'btn btn-sm btn-success']) . '<br><br>'
                        . FinishOrderOnlySumWidget::widget(['id_order' => $model->id]). '<br><br>'
                        . Html::a('Удалить ТС', Url::to([
                            '/order/canceled-by-vehicle',
                            'id_order' => $model->id,
                            'id_user' => $model->id_user,
                            'redirect' => '/logist/order'
                        ]),
                            ['data-confirm' => Yii::t('yii',
                                'Отказ от заказа может повлиять на Ваш рейтинг! Отказаться от заказа?'),
                                'data-method' => 'post',
                                'class' => 'btn btn-xs btn-warning'])
                        . '<br><br>'
                        . Html::a('Отменить', Url::to([
                            '/order/canceled-by-client',
                            'id_order' => $model->id,
                            'id_vehicle' => $model->id_vehicle,
                            'redirect' => '/logist/order'
                        ]),
                            [
                                'class' => 'btn-xs btn-warning',
                                ['data-confirm' => Yii::t('yii',
                                        'Заказ в процессе выполнения! 
                                Пожалуйста, перед нажатием кнопки "ОК" позвоните водителю, принявшему Ваш заказ и предупредите об отмене.<br><br> Водитель: ' .
                                        $model->vehicleFioAndPhone)
                                    . '<br><br><i> Водитель имеет возможность оценить корректность Ваших действий, что может повлиять на Ваш рейтинг Клиента.</i>',
                                    'data-method' => 'post']
                            ])
                        ;
                }

            ],
        ]
    ]); ?>
</div>

<script>
    $(function () {
        $('#close_button').on('click', function () {
            $('.modal').modal('hide');
        })

        $('#cost').on('input keyup', function () {
            $('#cost_vehicle').val($(this).val());
        })
    })
</script>