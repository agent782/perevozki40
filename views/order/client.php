
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
                'filter' => Html::activeCheckboxList($searchModel, 'statuses',
                    \app\models\Order::getStatusesArray()),

            ],
            [
                'attribute' => 'startAndValidDateString',
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
            'vehicleType.type',

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
