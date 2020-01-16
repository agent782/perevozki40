<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 10.04.2019
 * Time: 13:56
 */
use yii\bootstrap\Html;
//echo Html::radioList('a', $vehicles);
use app\models\PriceZone;
use app\models\setting\SettingVehicle;
use yii\helpers\Url;

/* @var $modelOrder \app\models\Order*/

?>
<?= Html::a('Назад', '/logist/order', ['class' => 'btn btn-success'])?>
<h2>Заказ №<?=$modelOrder->id?></h2>
<?= $modelOrder->getFullNewInfo(true)?>
<?php
echo \kartik\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => ($searchModel) ? $searchModel : false,
//    'pjax' => true,
    'striped' => true,
    'hover' => true,
//    'panel' => ['type' => 'primary', 'heading' => 'Grid Grouping Example'],
    'toggleDataContainer' => ['class' => 'btn-group mr-2'],
    'columns' => [
        ['class' => 'kartik\grid\SerialColumn'],
        [
            'attribute' => 'id_user',
//            'group' => true,
            'format' => 'raw',
            'value' => function(\app\models\Vehicle $model){
                return '"' . $model->profile->old_id . '" ' .$model->profile->fioFull
                    . ' ' .  $model->profile->phone .' (ID ' . $model->profile->id_user . ')';
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
                $rate = PriceZone::findOne(['id' => $model->getMinRate($modelOrder)->id, 'status' => PriceZone::STATUS_ACTIVE]);
                $rate = $rate->getPriceZoneForCarOwner($model->id_user);

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

