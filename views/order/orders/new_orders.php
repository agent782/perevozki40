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
            'attribute' => 'statusText',
            'filter' => Html::activeCheckboxList($searchModel, 'statuses', \app\models\Order::getStatusesArray()),

        ],
        [
            'attribute' => 'datetime_start',
            'options' => [
//                    'style' =>'width: 100px',
            ],
            'contentOptions'=>['style'=>'white-space: normal;']
        ],
        [
            'label' => 'Маршрут',
            'format' => 'raw',
            'value' => function($data){
                $route = $data->route;
                $return = $route->startCity . ' -';
                for($i = 1; $i<9; $i++){
                    $attribute = 'route' . $i;
                    if($route->$attribute) $return .= '... -';
                }
                $return .=  ' '.$route->finishCity ;
                return $return;
            },
        ],
//        'vehicleType.type',
        [
            'label' => 'Подходит для Ваших ТС',
            'format' => 'raw',
            'value' => function($model){
                $res = '';
                $vehicles = \app\models\Vehicle::find()
                    ->where([
                        'id_user' => Yii::$app->user->id,
                        'status' => [Vehicle::STATUS_ACTIVE, Vehicle::STATUS_ONCHECKING]
                    ])->all();
                if(!count($vehicles)) return;
                foreach ($vehicles as $vehicle) {
                    if ($vehicle->canOrder($model)) {
                        $res .= $vehicle->regLicense->reg_number . '<br>';
                    }
                }
                return $res;
            }
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