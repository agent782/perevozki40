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
use app\models\Vehicle;
use app\models\CalendarVehicle;

/* @var $modelOrder \app\models\Order*/

?>
<?= Html::a('Назад', '/logist/order', ['class' => 'btn btn-xs btn-info'])?>

<?= Html::a('Отсортировать',Url::to([
    '/logist/order/find-vehicle',
    'redirect' => '/order/accept-order',
    'id_order' => $modelOrder->id,
    'redirectError' => '/logist/order',
    'sort' => true
]), ['class' => 'btn btn-xs btn-success'])?>
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
        [
            'format' => 'raw',
            'value' => function(Vehicle $model, $key, $index) use ($modelOrder){
                $return = '';
                if($distance = $model->hasOrderOnDate($modelOrder->datetime_start)){
                    if($distance > 120){
                        $return = '<br>' . Html::icon('glyphicon glyphicon-ban-circle');
                    } else {
                        $return = '<br>' . Html::icon('glyphicon glyphicon-adjust');
                    }
                }
                return ($index + 1) . $return;
            }
        ],
        [
            'label' => 'Статус ТС',
            'format' => 'raw',
            'value' => function(Vehicle $vehicle) use ($date_day){
                $calendarVehicle = $vehicle->getCalendarVehicle()
                    ->andWhere(['date' => $date_day])->one();
                if(!$calendarVehicle){
                    $calendarVehicle = new CalendarVehicle();
                }
//                return $calendarVehicle->status .  ' ' . $date_day;
                return Html::radioList(
                    'calendar' . $vehicle->id,
                    $calendarVehicle->status,
                    CalendarVehicle::getArrayListStatuses(),
                    [
                       'style' => 'font-size: 8px',
                        'id' => 'calendar' . $vehicle->id,
                        'onchange' => '
                                            $.ajax({
                                                url: "/calendar-vehicle/ajax-change-status",
                                                type: "POST",
                                                dataType: "json",
                                                data: {
                                                    date: ' . $date_day . ',
                                                    id_vehicle: ' . $vehicle->id . ',
                                                    status: $(this).find("input:checked").val()
                                                },
                                                
                                                success: function(data){
//                                                    alert(data);
                                                },
                                                error: function(){
                                                    alert("Ошибка на сервере!")
                                                }
                                         });
                        '
                    ]);
            },
        ],
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

