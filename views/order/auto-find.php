<?php
    use yii\bootstrap\Html;
    use yii\widgets\Pjax;
    use app\models\Vehicle;
    use app\components\functions\functions;
    use app\models\CalendarVehicle;
    use app\models\PriceZone;

$script = <<< JS

$("document").ready(function(){
     setInterval(() => $.pjax.reload({container:'#auto-find'}), 20*1000);
});
JS;
$this->registerJs($script);

$this->title = $modelOrder->id . ' № заказ';
?>



<?=
    \kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'pjax' => true,
        'pjaxSettings' => [
            'options' => [
                'id' => 'auto-find'
            ]
        ],
        'columns' => [
            [
                'label' => $time,
                'value' => function (Vehicle $model) use ($modelOrder){
                    return ($time_send = $model->profile->getAlertNewOrder($modelOrder->id))
                        ? $time_send->create_at
                        : ''
                        ;
                }
            ],
            [
                'label' => 'Статус',
                'format' => 'raw',
                'value' => function (Vehicle $vehicle) use ($modelOrder){
                    $date_day = functions::DayToStartUnixTime($modelOrder->datetime_start);
                    $calendarVehicle = $vehicle->getCalendarVehicle()
                        ->andWhere(['date' => $date_day])->one();
                    if(!$calendarVehicle){
                        $calendarVehicle = new CalendarVehicle();
                    }
                    return $calendarVehicle->status;
                }
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
                    $rate = PriceZone::findOne(['id' => $model->getMinRate($modelOrder)->id,
                        'status' => PriceZone::STATUS_ACTIVE]);
                    $rate = $rate->getPriceZoneForCarOwner($model->id_user);

                    return $rate->getTextWithShowMessageButton($modelOrder->route->distance, true);
                }
            ],
        ]
    ])
?>

