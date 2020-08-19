<?php
    /* @var \yii\web\View $this
     * @var Order $modelOrder

     */

    use yii\bootstrap\Html;
    use yii\widgets\Pjax;
    use app\models\Vehicle;
    use app\components\functions\functions;
    use app\models\CalendarVehicle;
    use app\models\PriceZone;
    use app\models\Order;
    use yii\helpers\Url;
    use app\components\widgets\ShowMessageWidget;

$this->registerJs(new \yii\web\JsExpression('
    $("document").ready(function() {
    
     setInterval(function (){
            if($("#status").val() == '. Order::STATUS_NEW .'
                || $("#status").val() == '. Order::STATUS_IN_PROCCESSING .'
            ){
                $.pjax.reload({container: "#auto-find"})
            }
         }
          , 20*1000);
     setInterval(
            function() {
                 $.ajax({
                     url : "/order/ajax-refresh-panel",
                     dataType: "json",
                     type: "POST",
                     data: {id_order : '. $modelOrder->id .'},
                     success: function(data) {
                       
                       if(data.status == '. Order::STATUS_NEW .'
                            || data.status == '. Order::STATUS_IN_PROCCESSING .'
                        ){
                            if(data.auto_find){
                                $("#icon_play").attr("hidden", true);
                                $("#icon_pause").attr("hidden", false);

                            } else {
                                $("#icon_play").attr("hidden", false);
                                $("#icon_pause").attr("hidden", true);
                            }
                                                                                   
                        } else {
                            $("#icon_play, #icon_pause").attr("hidden", true);
                              
                        }
                         $("#time").html(data.time);
                        $("#statusText").html(data.statusText);
                        
                        $("#status").val(data.status);
                     },
                     error : function() {
//                       alert ("Ошибка на сервере");
                     }
                 });
                 
                
            }
         , 1*1000
     );
     
});    
'));


$this->title = $modelOrder->id . ' № заказ';
?>
<br>
<b class = "h3" id="statusText">
    <?= $modelOrder->statusText;?>
</b>


<div class = "h3">
    <?= Html::a(Html::icon('play-circle'), '#', [
        'id' => 'icon_play',
        'hidden' => ($modelOrder->auto_find) ? true : false,
        'onclick' => new \yii\web\JsExpression('
            $.ajax({
                     url : "/logist/order/ajax-start-auto-find",
                     dataType: "json",
                     type: "POST",
                     data: {id_order : '. $modelOrder->id .'},
                     success: function(data) {

                     },
                     error : function() {
                       alert ("Ошибка на сервере");
                     }
                 });
        ')
    ])?>
    <?= Html::a(Html::icon('pause'), '#', [
        'id' => 'icon_pause',
        'hidden' => (!$modelOrder->auto_find) ? true : false,
        'onclick' => new \yii\web\JsExpression('
            $.ajax({
                     url : "/logist/order/ajax-stop-auto-find",
                     dataType: "json",
                     type: "POST",
                     data: {id_order : '. $modelOrder->id .'},
                     success: function(data) {
                       
                     },
                     error : function() {
                       alert ("Ошибка на сервере");
                     }
                 });
        ')
    ])?>

    <div id="time">
        <?php
            $reset_hidden = true;
            if(in_array($modelOrder->status, [Order::STATUS_NEW, Order::STATUS_IN_PROCCESSING])){
                echo $time;
                $reset_hidden = false;
            }
            echo ' ' . Html::a(Html::icon('reset'), '#', [
                        'id' => 'icon-reset',
                        'hidden' => $reset_hidden,
                    ]);

        ?>
    </div>
</div>

<div id="status" hidden><?= $modelOrder->status ?></div>
<br>


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
                'value' => function (Vehicle $model) use ($modelOrder){
                    $alerts = $modelOrder->alert_car_owner_ids;
                    if(is_array($alerts) && $alerts) {
                        if (array_key_exists($model->id_user, $alerts)) {
                            return date('d.m.Y H:i', $alerts[$model->id_user]);
                        }
                    }

//                    return ($time_send = $model->profile->getAlertNewOrder($modelOrder->id))
//                        ? $time_send->create_at
//                        : ''
//                        ;
                }
            ],
            [
                'label' => 'Статус',
                'format' => 'raw',
                'value' => function (Vehicle $vehicle) use ($modelOrder){
                    $return = '';
                    if($calendarVehicle = $vehicle->getCalendarVehicle($modelOrder->datetime_start)->one()){
                        $return = $calendarVehicle->statusText;
                    }
                    $return .= ' ' . $vehicle->hasOrderOnDate($modelOrder->datetime_start, true);
                    return $return;
                }
            ],
            [
                'attribute' => 'id_user',
//            'group' => true,
                'format' => 'raw',
                'value' => function(\app\models\Vehicle $model) use ($modelOrder){
                    $rate = PriceZone::findOne(['id' => $model->getMinRate($modelOrder)->id,
                        'status' => PriceZone::STATUS_ACTIVE]);
                    if($rate) {
                        $rate = $rate->getPriceZoneForCarOwner($model->id_user);
                    }
                    return ShowMessageWidget::widget([
                        'ToggleButton' => [
                            'label' => '"' . $model->profile->old_id . '" ' .$model->profile->fioFull
                                . ' ' .  $model->profile->phone .' (ID ' . $model->profile->id_user . ')'
                        ],
                        'helpMessage' => $model->fullInfo
                            . '<br>'
                            . $rate->getTextWithShowMessageButton($modelOrder->route->distance, true)
                    ]);
                    return '"' . $model->profile->old_id . '" ' .$model->profile->fioFull
                        . ' ' .  $model->profile->phone .' (ID ' . $model->profile->id_user . ')';
                },
//            'groupedRow' => true,
//            'groupOddCssClass' => 'kv-grouped-row',  // configure odd group cell css class
//            'groupEvenCssClass' => 'kv-grouped-row', // configure even group cell css class

            ],
//            [
//                'attribute' => 'fullInfo',
//                'format' => 'raw'
//            ],
//            [
//                'label' => 'Тариф',
//                'format' => 'raw',
//                'value' => function($model) use ($modelOrder){
//                    $rate = PriceZone::findOne(['id' => $model->getMinRate($modelOrder)->id,
//                        'status' => PriceZone::STATUS_ACTIVE]);
//                    $rate = $rate->getPriceZoneForCarOwner($model->id_user);
//
//                    return $rate->getTextWithShowMessageButton($modelOrder->route->distance, true);
//                }
//            ],
        ]
    ])
?>

