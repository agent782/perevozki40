<?php

use kartik\grid\GridView;
use yii\widgets\ActiveForm;
use app\models\CalendarVehicle;
use yii\bootstrap\Html;
use app\models\Vehicle;
use app\components\functions\functions;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CalendarVehicleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->registerJs(new \yii\web\JsExpression('
        $(function(){
        var arr_ids = $("#ids_vehicles").text().split(" ");
        for(var i in arr_ids) {
            $("#slide_vehicle_" + arr_ids[i]).hide();
            
            $("#pointer_vehicle_" + arr_ids[i]).click(function () {
                $(this).siblings()
                    .children()
                    .slideUp("slow");
               if($(this).children().is(":hidden")){
                    $(this)
                        .children()
                        .slideDown("slow");
                } else {
                    $(this)
                        .children()
                        .slideUp("slow");
                }
            });

               

        }
    });

    '));

$this->title = 'Календарь занятости';
?>
<div class="calendar-vehicle-index">

    <h2><?= Html::encode($this->title) ?></h2>
    <br>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
    foreach ($Vehicles as $key => $vehicle){
        echo
            '<div id="pointer_vehicle_' . $key . '" style = "cursor: pointer;">' .
            Vehicle::findOne($key)->brandAndNumber .
            '<div id="slide_vehicle_' . $key . '">' .
            GridView::widget([
                'dataProvider' => $vehicle,
                'pjax'=>true,
                'pjaxSettings' => [
                    'options' => [
                        'id' => 'pjax_calendar_' . $key
                    ]
                ],
                'responsiveWrap' => false,
                'summary' => false,
                'columns' => [
                    [
                        'value' => function($model){
                            return functions::rus_date('l d.m.Y', $model['date']);
                        },
                        'options' => ['style' => 'width: 100px']
                    ],
                    [
                        'format' => 'raw',
                        'value' => function($model, $key, $index){
                            $date = $model['date'];
                            return Html::radioList('calendar' . $model['id_vehicle'] . $index,
                                $model['status'],
                                CalendarVehicle::getArrayListStatuses(),
                                [
                                    'onchange' =>
                                        '
                                        $.ajax({
                                                url: "/calendar-vehicle/ajax-change-status",
                                                type: "POST",
                                                dataType: "json",
                                                data: {
                                                    date: ' . $model["date"] . ',
                                                    id_vehicle: ' . $model["id_vehicle"] . ',
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
                        }
                    ]

                ]
            ])
            . '</div> </div>'
        ;
    }
    ?>
    <div id="ids_vehicles" hidden><?=$ids_vehicles?></div>
</div>