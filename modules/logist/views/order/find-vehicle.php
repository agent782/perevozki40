<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 10.04.2019
 * Time: 13:56
 */
use yii\bootstrap\Html;
echo Html::radioList('a', $vehicles);
use app\models\PriceZone;
use app\models\setting\SettingVehicle;
use yii\helpers\Url;

/* @var $modelOrder \app\models\Order*/

?>
<?= Html::a('Назад', '/logist/order', ['class' => 'btn btn-success'])?>
<h2>Заказ №<?=$modelOrder->id?></h2>
<?= $modelOrder->fullNewInfo?>
<?php
echo \kartik\grid\GridView::widget([
    'dataProvider' => $dataProvider,
//    'pjax' => true,
    'striped' => true,
    'hover' => true,
//    'panel' => ['type' => 'primary', 'heading' => 'Grid Grouping Example'],
    'toggleDataContainer' => ['class' => 'btn-group mr-2'],
    'columns' => [
        ['class' => 'kartik\grid\SerialColumn'],
        [
            'attribute' => 'id_user',
            'group' => true,
            'format' => 'raw',
            'value' => function($model){
                return $model->profile->fioFull . ' (ID ' . $model->profile->id_user . ')';
            },
//            'groupedRow' => true,
//            'groupOddCssClass' => 'kv-grouped-row',  // configure odd group cell css class
//            'groupEvenCssClass' => 'kv-grouped-row', // configure even group cell css class

        ],
        [
            'attribute' => 'fullInfo',
            'format' => 'raw'
        ],
        [
            'label' => 'Тариф',
            'format' => 'raw',
            'value' => function($model) use ($modelOrder){
                $rate = PriceZone::findOne($model->getMinRate($modelOrder));
                $rate = $rate->getWithDiscount(SettingVehicle::find()->limit(1)->one()->price_for_vehicle_procent);

                return $rate->getTextWithShowMessageButton($modelOrder->route->distance, true);
            }
        ],
        [
            'label' => 'Действия',
            'format' => 'raw',
            'value' => function ($model) use ($modelOrder){
                return Html::a('Принять',Url::to([
                        '/order/accept-order',
                        'id_order' => $modelOrder->id,
                        'id_user' => $model->id_user,
                        'redirect' => '/logist/order',
                        'id_vehicle' => $model->id,
                    ]), ['class' => 'btn btn-primary'])
                    ;
            }
        ]
    ]
]);
?>
<?= Html::a('Назад', '/logist/order', ['class' => 'btn btn-success'])?>

