<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 05.01.2019
 * Time: 12:28
 */
use yii\grid\GridView;
//use kartik\grid\GridView;
use app\models\Order;
use yii\bootstrap\Html;
use app\models\Vehicle;
use yii\helpers\Url;
use app\models\setting\SettingVehicle;
use yii\bootstrap\Tabs;
use app\components\widgets\ShowMessageWidget;
use app\models\Payment;
use app\models\PriceZone;
?>
<div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider_in_process,
//    'filterModel' => $searchModel,
//        'bordered' => true,
//        'striped' => false,
//        'responsive'=>true,
//        'floatHeader'=>false,
//        'responsiveWrap' => false,
//        'pjax'=>true,
        'columns' => [
            'id',
            [
                'attribute' =>'datetime_start',
                'format' => 'raw',
                'contentOptions' => [
                    'class' => 'h5'
                ]
            ],
            [
                'label' => 'ТС',
                'format' => 'raw',
                'value' => function($order){
                    return $order->getFullInfoAboutVehicle(true,true,true,true);
                },
                'contentOptions' => [
//                    'style' => 'max-width: 300px; white-space: normal;'
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
                'value' => function(Order $model){
                    return $model->getShortInfoForClient(true);
                }
            ],
            [
                'label' => 'Заказчик',
                'format' => 'raw',
                'attribute' => 'clientInfo',
                'value' => function(Order $modelOrder){
                    if($modelOrder->id_user == $modelOrder->id_car_owner && $modelOrder->re){
                        return '"Повторный заказ" <br>' . $modelOrder->comment;
                    }
                    $re = ($modelOrder->re)?'"Повторный заказ"':'';
                    return  $re . $modelOrder->getClientInfo();
                }
            ],
            [
                'label' => 'Тариф',
                'format' => 'raw',
                'attribute' => 'id_pricezone_for_vehicle',
                'value' => function(Order $modelOrder){
                    return \app\models\PriceZone::findOne(['unique_index' =>$modelOrder
                        ->id_pricezone_for_vehicle])
                        ->getWithDiscount($modelOrder->getVehicleProcentPrice())
                        ->getTextWithShowMessageButton($modelOrder->route->distance, true);
                }
            ],
            [
                'attribute' => 'paymentText',
                'format' => 'raw',
                'value' => function(Order $model){
                    $return = $model->paymentText
                        . ShowMessageWidget::widget([
                            'helpMessage' => $model->typePayment->description
                        ])
                        . '<br>'

                        ;
                    if($model->type_payment == Payment::TYPE_BANK_TRANSFER){
                        $return .= '(Стоимость для Клиента на ' . (9 - $model->discount) . ' % выше. ';
                        $return .= PriceZone::findOne(['unique_index' => $model
                            ->id_pricezone_for_vehicle])
                            ->getTextWithShowMessageButton($model->route->distance, true, $model->discount);
                    }
                    return $return;
                }
            ],
            [
                'label' => 'Действия',
                'format' => 'raw',
                'value' => function ($model){
                    $return = '';
                    $return .= Html::a('Заказ выполнен', Url::to([
                        '/order/finish-by-vehicle',
                        'id_order' => $model->id,
                    ]),['class' => 'btn btn-sm btn-success']) . '<br><br>';
                    $return .= Html::a('Отказаться', Url::to([
                        '/order/canceled-by-vehicle',
                        'id_order' => $model->id,
                        'id_user' => Yii::$app->user->id,
                    ]),
                        ['data-confirm' => Yii::t('yii',
                            'Отказ от заказа может повлиять на Ваш рейтинг! Отказаться от заказа?'),
                        'data-method' => 'post',
                        'class' => 'btn btn-xs btn-warning']);
                    return $return;
                },
            ],
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>