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
//            [
//                'class' => 'kartik\grid\ExpandRowColumn',
//                'value' => function ($model, $key, $index, $column) {
//
//                    return GridView::ROW_COLLAPSED;
//                },
//                'enableRowClick' => true,
//                'allowBatchToggle'=>true,
//                'detail'=>function ($model) {
//                    return 'TEST';
////                    return Yii::$app->controller->renderPartial('view', ['model'=>$model]);
//                },
//                'detailOptions'=>[
//                    'class'=> 'kv-state-enable',
//                ],
//            ],
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
                'format' => 'raw'
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
                'value' => function($model){
                    $fio = ($model->driver)
                        ? $model->driver->fio
                        : $model->profile->fioFull;
                    return $model->vehicle->brandAndNumber
                        . ' (' . $fio . ')';
                }
            ],
            [
                'label' => 'Заказчик',
                'format' => 'raw',
                'value' => function(Order $modelOrder){
                    return ($modelOrder->paid_status == Order::PAID_YES)
                        ? $modelOrder->getClientInfoWithoutPhone()
                        : $modelOrder->getClientInfo()
                        ;
                }
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