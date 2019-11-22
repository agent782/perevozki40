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
    <h4>Завершенные</h4>
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
                    return Yii::$app->controller->renderPartial('client/view', ['model'=>$model]);
                },
                'detailOptions'=>[
                    'class'=> 'kv-state-enable',
                ],
            ],
            'id',
            'datetime_finish',
            [
                'label' => 'Сумма к оплате',
                'attribute' => 'cost_finish',
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
                        if($order->type_payment == \app\models\Payment::TYPE_BANK_TRANSFER){
                            $return .= '<br>Документы оформляются...';
                        }

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
                'value' => function(Order $model){
                    $fio = ($model->driver)
                        ? $model->driver->fio
                        : $model->carOwner->fioFull;
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