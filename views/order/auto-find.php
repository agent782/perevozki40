<?php
    use yii\bootstrap\Html;
    use yii\widgets\Pjax;
    use app\models\Vehicle;
    use app\components\functions\functions;
    use app\models\CalendarVehicle;

$script = <<< JS

$("document").ready(function(){
     setInterval(() => $.pjax.reload({container:'#auto-find'}), 5*1000);
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
            ]
        ]
    ])
?>

