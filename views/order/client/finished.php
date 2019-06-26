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
use app\models\Invoice;
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
            'real_datetime_start',
            [
                'label' => 'Сумма к оплате',
                'attribute' => 'finishCost',
                'format' => 'raw'
            ],
            [
                'attribute' => 'paidText',
                'format' => 'raw'
            ],
            [
                'attribute' => 'paymentText',
                'format' => 'raw',
                'value' => function(Order $order){
                    $return = $order->paymentText;
                    if($order->invoice){
                        if($order->invoice->urlFull){
                            $return .= '<br>' . Html::a('Счет №' . $order->invoice->number,
                                    Url::to(['/finance/invoice/download',
                                        'pathToFile' => $order->invoice->urlFull,
                                        'redirect' => Url::to(['/order/client'])
                                    ]),
                                    ['title' => 'Скачать', 'data-pjax' => "0"]
                                );
                        }
                    } else {
                        $return .= '<br>Документы оформляются...';
                    }

                    if($order->certificate){
                        $return .= '<br>' . Html::a('Акт №' . $order->certificate->number,  Url::to(['/finance/invoice/download',
                                'pathToFile' => $order->certificate->urlFull,
                                'redirect' => '/order/client'
                            ]),
                                ['title' => 'Скачать', 'data-pjax' => "0"]);
                    }


                    return $return;
                }
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
                'attribute' => 'clientInfo'
            ],
            [
                'label' => 'Действия',
                'format' => 'raw',
            ],

        ]
    ]); ?>
</div>