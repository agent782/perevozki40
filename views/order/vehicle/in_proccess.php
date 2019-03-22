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
use app\models\setting\SettingVehicle;
use yii\bootstrap\Tabs;
?>
<div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider_in_process,
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
                'label' => 'ТС',
                'format' => 'raw',
                'attribute' => 'fullInfoAboutVehicle'
            ],
            [
                'label' => 'Маршрут',
                'format' => 'raw',
                'attribute' => 'route.fullRoute'
            ],
            [
                'label' => 'Информация о заказе',
                'format' => 'raw',
                'attribute'=>'shortInfoForClient'
            ],
            [
                'label' => 'Заказчик',
                'format' => 'raw',
                'attribute' => 'clientInfo'
            ],
            [
                'label' => 'Тариф',
                'format' => 'raw',
                'attribute' => 'id_pricezone_for_vehicle',
                'value' => function($modelOrder){
                    return \app\models\PriceZone::findOne($modelOrder
                        ->id_pricezone_for_vehicle)
                        ->getWithDiscount(SettingVehicle::find()->limit(1)->one()->price_for_vehicle_procent)
                        ->getTextWithShowMessageButton($modelOrder->route->distance);
                }
            ],
            [
                'attribute' => 'paymentText',
                'format' => 'raw'
            ],
            [
                'label' => 'Действия',
                'format' => 'raw',
                'value' => function ($model){
                    $return = '';
                    $return .= Html::a('Заказ выполнен', Url::to([
                        '/order/finish-by-vehicle',
                        'id_order' => $model->id,
                    ]),['class' => 'btn btn-sm btn-success']) . '<br><br>';
                    $return .= Html::a('Отказаться', Url::to([
                        '/order/canceled-by-vehicle',
                        'id_order' => $model->id,
                        'id_user' => Yii::$app->user->id,
                    ]),
                        ['data-confirm' => Yii::t('yii',
                            'Отказ от заказа может повлиять на Ваш рейтинг! Отказаться от заказа?'),
                        'data-method' => 'post',
                        'class' => 'btn btn-xs btn-warning']);
                    return $return;
                },
            ],
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>