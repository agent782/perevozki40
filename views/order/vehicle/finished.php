<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 14.03.2019
 * Time: 15:00
 */
use kartik\grid\GridView;
use app\models\Order;
use yii\bootstrap\Html;
use app\models\Vehicle;
use yii\helpers\Url;
use yii\bootstrap\Tabs;
use app\models\Tip;
?>
<div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider_arhive,
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
                    return Yii::$app->controller->renderPartial('vehicle/view', ['model'=>$model]);
                },
                'detailOptions'=>[
                    'class'=> 'kv-state-enable',
                ],
            ],
            'id',
            [
                'label' => 'Время начала/завершения',
                'format' => 'raw',
                'value' => function($model){
                    return
                        $model->real_datetime_start
                        . ' / '
                        . $model->datetime_finish
                        ;
                }
            ],
            [
                'label' => 'Сумма (р.)',
                'attribute' => 'cost_finish_vehicle',
                'format' => 'raw'
            ],
            [

                'attribute' => 'paidText',
                'format' => 'raw',
                'value' => function (Order $order){
                    return ($order->paid_status == $order::PAID_YES_AVANS)
                        ? $order->paidText . ' (' . $order->avans_client . ')'
                        : $order->paidText;
                }
            ],
            [
                'attribute' => 'paymentText',
                'format' => 'raw'
            ],
            [
                'label' => 'Маршрут',
                'format' => 'raw',
                'value' => function($model){
                    return $model->getShortRoute(true);
                }
            ],
            [
                'label' => 'ТС и водитель',
                'format' => 'raw',
                'value' => function(Order $model){
                    $driver = $model->driver;
                    if($driver) {
                        $fio = $driver->fio;
                    } else {
                        $car_owner = $model->carOwner;
                        if($car_owner){
                            $fio = $car_owner->fioFull;
                        }
                    }
                    return $model->vehicle->brandAndNumber
                        . ' (' . $fio . ')';
                }
            ],
            [
                'label' => 'Заказчик',
                'format' => 'raw',
                'value' => function(Order $modelOrder){
                    if($modelOrder->id_user == $modelOrder->id_car_owner){
                        return '"Повторный заказ" <br>' . $modelOrder->comment;
                    }
                    $re = ($modelOrder->re)?'"Повторный заказ"':'';
                    return ($modelOrder->paid_status == Order::PAID_YES)
                        ? $re . $modelOrder->getClientInfoWithoutPhone()
                        : $re . $modelOrder->getClientInfo()
                        ;
                }
            ],
            [
                'attribute' => 're',
                'format' => 'raw',
                'label' => Html::icon('star'). ' ' . Tip::getTipButtonModal('Order', 're'),
                'value' => function (Order $Order){
                    return ($Order->re)?Html::icon('star'):'';
                },
                'encodeLabel' => false
            ],
            [
                'label' => 'Действия',
                'format' => 'raw',
                'value' => function($model){
                        return

                            ;
                }
            ]
        ]
    ]); ?>
</div>