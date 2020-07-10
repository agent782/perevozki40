<?php

/* @var $this \yii\web\View
*
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
use app\models\CalendarVehicle;
use app\components\functions\functions;

$this->registerJs("
    setInterval(() => $.pjax.reload({container:'#pjax_new_orders'}), 2*60*1000); 
");
?>
<div>
<?= GridView::widget([
    'dataProvider' => $dataProvider_newOrders,
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
        'id',
        [
            'label' => 'Дата/время',
            'attribute' => 'datetime_start',
            'contentOptions'=>[
                'style'=>'white-space: normal;',
                'class' => 'h5'
            ]
        ],
        [
            'label' => 'Маршрут',
            'format' => 'raw',
            'attribute' => 'route.fullRoute'
//            'value' => function($data){
//                $route = $data->route;
//                $return = $route->startCity . ' -';
//                for($i = 1; $i<9; $i++){
//                    $attribute = 'route' . $i;
//                    if($route->$attribute) $return .= '... -';
//                }
//                $return .=  ' '.$route->finishCity ;
//                return $return;
//            },
        ],
        [
            'label' => 'Информация',
            'format' => 'raw',
            'value' => function(Order $model){
                return $model->getShortInfoForClient(true);
            }
        ],
        [
            'label' => 'Подходит для Ваших ТС',
            'format' => 'raw',
            'value' => function(Order $model){
                $res = '';
                $vehicles = \app\models\Vehicle::find()
                    ->where([
                        'id_user' => Yii::$app->user->id,
                        'status' => [Vehicle::STATUS_ACTIVE, Vehicle::STATUS_ONCHECKING]
                    ])->all();
                if(!count($vehicles)) return;
                foreach ($vehicles as $vehicle) {
                    if ($vehicle->canOrder($model)) {
                        $calendar = $vehicle->getCalendarVehicle($model->datetime_start)->one();
                        $res .= $vehicle->regLicense->reg_number . ' ';
                        if ($calendar) {
                            $status = $calendar->status;
                        } else {
                            $status = "";
                        }
                        $res .= Html::radioList('calendar' . $vehicle->id,
                            $status,
                            CalendarVehicle::getArrayListStatuses(),
                            [
                                'style' => 'font-size: 8px; display:inline-block',
                                'onchange' =>
                                    '
                                        $.ajax({
                                                url: "/calendar-vehicle/ajax-change-status",
                                                type: "POST",
                                                dataType: "json",
                                                data: {
                                                    date: ' . functions::DayToStartUnixTime($model->datetime_start) . ',
                                                    id_vehicle: ' . $vehicle->id . ',
                                                    status: $(this).find("input:checked").val()
                                                },
                                                
                                                success: function(data){
//                                                        alert(data);
                                                },
                                                error: function(){
                                                    alert("Ошибка на сервере!")
                                                }
                                         });
                                    '
                            ]) . '<br>';
                    }
                }
                return $res;
            },
            'contentOptions' => [
                'class' => 'h4'
            ]
        ],
//        [
//            'label' => 'Заказчик',
//            'format' => 'raw',
//            'attribute' => 'clientInfo',
//            'value' => function (Order $model){
//                return $model->getClientInfo(true, false);
//            }
//        ],
        [
            'label' => 'Тип оплаты',
            'attribute' => 'paymentText',
            'format' => 'raw'
        ],
        [
            'label' => '',
            'format' => 'raw',
            'value' => function ($model){
                return Html::a('Принять',Url::to([
                    '/order/accept-order',
                    'id_order' => $model->id,
                    'id_user' => Yii::$app->user->id,
                ]), ['class' => 'btn btn-primary']);
            }
        ],
//        ['class' => 'yii\grid\ActionColumn'],
    ],
]); ?>
</div>