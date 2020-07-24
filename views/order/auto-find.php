<?php
    use yii\bootstrap\Html;
    use yii\widgets\Pjax;
    use app\models\Vehicle;
    use app\components\functions\functions;
    use app\models\CalendarVehicle;

$script = <<< JS

$("document").ready(function(){
        setTimeout(function(){
            $.pjax.reload({container:"#refresh-time"});  //Reload GridView
        },1000);
});
JS;
$this->registerJs($script);
?>
<?php Pjax::begin([
    'id' => 'refresh-time',
//    'timeout' => 1
]);?>

<div class="container">
    <br>
    <div class="h2"> <?= $time;?></div>
</div>


<?=
    \kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'pjax' => false,
        'columns' => [
            'id',
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

<?php Pjax::end()?>
