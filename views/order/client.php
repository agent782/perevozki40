<?php
use yii\grid\GridView;
use app\models\Order;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Заказы (Клиент)';
$this->params['breadcrumbs'][] = $this->title;
var_dump($searchModel->paid_statuses);
?>
<div class="order-index">
    <p>
        <?= Html::a('Сделать новый заказ', ['create'], ['class' => 'btn btn-lg btn-danger']) ?>
    </p>
    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<!--    --><?php //\yii\widgets\Pjax::begin([
//            'id' => 'pjax'
//        ]);

    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'options' => [
                    'style' =>'width: 80px;'
                ],
            ],
            [
                'attribute' => 'startAndValidDateString',
                'options' => [
                    'style' =>'width: 100px;'
                ]
            ],
            [
                'label' => 'ТС',
                'format' => 'raw',
                'value' => function($data){
                    $bTypies = '';
                    $lTypies = '';
                    $return  = $data->vehicleType->type;
                    $return .= '. ';
                    foreach ($data->bodyTypies as $bodyType){
                        $bTypies .=  $bodyType->body . ', ';
                    }
                    $bTypies = substr($bTypies, 0, -2) . '. ';
                    $return .= $bTypies;
                    if(count($data->loadingTypies)){
                        foreach ($data->loadingTypies as $loadingType){
                            $lTypies .= $loadingType->type . ', ';
                        }
                        $lTypies = substr($lTypies, 0, -2) . '. ';
                        $return .= $lTypies;
                    }
                    return $return;
                },
                'contentOptions' => [
                    'class' => 'minRoute'
                ]
            ],
            [
                'label' => 'Маршрут',
                'format' => 'raw',
                'value' => function($data){
                    $route = $data->route;
                    $return = $route->routeStart . ' - ';
                    for($i = 1; $i<9; $i++){
                        $attribute = 'route' . $i;
                        if($route->$attribute) $return .= '<br>- ' . $route->$attribute;
                    }
                    $return .=  '<br>- ' . $route->routeFinish ;
                    return $return;
                },
                'contentOptions' => [
                    'class' => 'minRoute'
                ]
            ],
//            'vehicleType.type',
            [
                'attribute' => 'statusText',
                'filter' => Html::activeCheckboxList($searchModel, 'statuses', \app\models\Order::getStatusesArray()),

            ],
            [
                'label' => 'ТС',
                'format' => 'raw',
                'value' => function ($data){
                    return ($data->vehicle)?
                        $data->vehicle->regLicense->brand->brand
                        . ' '
                        . $data->vehicle->regLicense->reg_number
                        :'Не назначено'
                        ;
                }
            ],
            [
                'attribute' => 'paidText',
                'filter' => Html::activeCheckboxList($searchModel, 'paid_statuses', [
                    Order::PAID_NO => 'Не оплачен',
                    Order::PAID_PARTIALLY => 'Частично оплачен',
                    Order::PAID_YES => 'Оплачен']),
            ],
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
            'create_at',
            // 'id_route',
            // 'id_route_real',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<!--    --><?php //\yii\widgets\Pjax::end()?>

</div>
