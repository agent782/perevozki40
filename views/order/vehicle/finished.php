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
    <h4>В процессе выполнения...</h4>
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
                    return Yii::$app->controller->renderPartial('view', ['model'=>$model]);
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
                'attribute' => 'finishCostForVehicle',
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
                    return $model->vehicle->brandAndNumber . ' (' . $model->driver->fio . ')';
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
            ],
            [
                'label' => 'Действия',
                'format' => 'raw',
                'value' => function($model){
                        return
                             Html::a(Html::icon('remove', ['class' => 'btn-lg','title' => 'Удалить заказ']), Url::to([
                                '/order/delete',
                                'id' => $model->id,
                                 'redirect' => '/order/client'
                            ]),
                                ['data-confirm' => Yii::t('yii',
                                    'Удалить безвозвратно?'),
                                    'data-method' => 'post'])
                            ;
                }
            ]
        ]
    ]); ?>
</div>