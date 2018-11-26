
<?php
//use yii\grid\GridView;
use kartik\grid\GridView;
use app\models\Order;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Заказы (Клиент)';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">
    <p>
        <?= Html::a('Сделать новый заказ', ['create'], ['class' => 'btn btn-lg btn-danger']) ?>
    </p>
    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php \yii\widgets\Pjax::begin([
            'id' => 'pjax'
        ]);

    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
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
//                'detailOptions'=>[
//                    'class'=> 'kv-state-enable',
//                ],

            ],
            [
                'attribute' => 'id',
                'options' => [
                    'style' =>'width: 80px;'
                ],
            ],
//            [
//                'attribute' => 'startAndValidDateString',
//                'options' => [
//                    'style' =>'width: 100px;'
//                ]
//            ],
//
//            [
////                'class' => 'kartik\grid\ExpandRowColumn',
//                'label' => 'Маршрут',
//                'format' => 'raw',
//                'value' => function($data){
//                    $route = $data->route;
//                    $return = $route->routeStart . ' - ';
//                    for($i = 1; $i<9; $i++){
//                        $attribute = 'route' . $i;
//                        if($route->$attribute) $return .= '<br>- ' . $route->$attribute;
//                    }
//                    $return .=  '<br>- ' . $route->routeFinish ;
//                    return $return;
//                },
//                'contentOptions' => [
//                    'class' => 'minRoute'
//                ]
//            ],
            'vehicleType.type',
//            [
//                'attribute' => 'statusText',
//                'filter' => Html::activeCheckboxList($searchModel, 'statuses', \app\models\Order::getStatusesArray()),
//
//            ],
//            [
//                'label' => 'ТС',
//                'format' => 'raw',
//                'value' => function ($data){
//                    return ($data->vehicle)?
//                        $data->vehicle->regLicense->brand->brand
//                        . ' '
//                        . $data->vehicle->regLicense->reg_number
//                        :'Не назначено'
//                        ;
//                }
//            ],
//            [
//                'attribute' => 'paidText',
//                'filter' => Html::activeCheckboxList($searchModel, 'paid_statuses', [
//                    Order::PAID_NO => 'Не оплачен',
//                    Order::PAID_PARTIALLY => 'Частично оплачен',
//                    Order::PAID_YES => 'Оплачен']),
//            ],
//            'id_vehicle_type',
//            'tonnage',
//            'length',
//            'width',
            // 'height',
            // 'volume',
            // 'longlength',
            // 'passengers',
            // 'ep',
            // 'rp',
            // 'lp',
            // 'tonnage_spec',
            // 'length_spec',
            // 'volume_spec',
            // 'cargo:ntext',
//            'datetime_start',
            // 'datetime_finish:datetime',
            // 'datetime_access:datetime',
//            'valid_datetime',
//            'create_at',
            // 'id_route',
            // 'id_route_real',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php \yii\widgets\Pjax::end()?>

</div>
