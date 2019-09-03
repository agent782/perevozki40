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
use yii\helpers\ArrayHelper;
?>

<div>
    <h4>Завершенные.</h4>
    <?= GridView::widget([
        'dataProvider' => $dataProvider_arhive,
        'filterModel' => $searchModel,
        'options' => [
            'class' => 'minRoute'
        ],
        'responsiveWrap' => false,
        'pjax' => true,
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
            'real_datetime_start',
            [
                'label' => 'Сумма',
                'format' => 'raw',
                'value' => function($model){
                    return $model->cost_finish . ' / ' . $model->cost_finish_vehicle;
                },
            ],
            [
                'class' => \kartik\grid\EditableColumn::class,
                'attribute' => 'paid_status',
                'value' => 'paidText',
                'filter' => Html::activeCheckboxList($searchModel, 'paid_status', $searchModel->getArrayPaidStatuses()),
                'editableOptions' => [
                    'inputType' => \kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                    'data' => $searchModel->getArrayPaidStatuses(),
                    'formOptions' => [
                        'action' => \yii\helpers\Url::to([ '/finance/order/pru' ])
                    ]
                ],
            ],

            [
                'class' => \kartik\grid\EditableColumn::class,
                'attribute' =>'type_payment',
                'format' => 'raw',
                'value' => function($model){
                    return $model->paymentMinText;
                },
                'filter' =>
                    Html::activeCheckboxList($searchModel, 'type_payments',
                        ArrayHelper::map(\app\models\TypePayment::find()->all(), 'id', 'min_text')
                    )
                ,
                'editableOptions' => [
                    'inputType' => \kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                    'data' =>                         ArrayHelper::map(\app\models\TypePayment::find()->all(), 'id', 'min_text')
                    ,
                    'formOptions' => [
                        'action' => \yii\helpers\Url::to([ '/finance/order/changePaymentType' ])
                    ]
                ],
                'filterOptions' => ['class' => 'minRoute'],
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
                    if($model->vehicle){
                        return  $model->vehicle->brandAndNumber
                        . ' (' . $model->profile->fioFull . ')';
                    }

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