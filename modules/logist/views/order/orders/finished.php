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
            'real_datetime_start',
            [
                'label' => 'Сумма',
                'format' => 'raw',
                'value' => function($model){
                    return $model->cost_finish . ' / ' . $model->cost_finish_vehicle;
                },
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
                'value' => function ($model){
                    $return = $model->clientInfo;
                    $return .= '<br>' . Html::a(Html::icon('edit', ['title' => 'Добавить юр. лицо', 'class' => 'btn-xs btn-primary']),
                            ['/logist/order/add-company', 'id_order' => $model->id]);

                    return $return;
                }
            ],
            [
                'label' => 'Действия',
                'format' => 'raw',
                'value' => function($model){
                        return
                            Html::a('Изменить результат', Url::to([
                                '/order/finish-by-vehicle',
                                'id_order' => $model->id,
                                'redirect' => '/logist/order'
                            ]),['class' => 'btn btn-sm btn-success'])
                            ;
                }
            ]
        ]
    ]); ?>
</div>