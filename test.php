<?php
    use yii\helpers\ArrayHelper;
    use yii\bootstrap\ActiveForm;
    use yii\bootstrap\Html;

//    use kartik\date\DatePicker;
//    use bootui\datetimepicker;
    use kartik\datetime\DateTimePicker;
//    use bootui\datetimepicker\DateTimepicker;
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 02.10.2017
 * Time: 13:23
 */

?>
<?php $this->registerJsFile('@web/js/route.js'); ?>

<?php $form = ActiveForm::begin([
        'id'=>'form',
//        'action'=>'test2',
]);

?>

<hr>
    <div id="typeVehicle">
        <?= $form->field($order, 'id_vehicle_type')->dropDownList(ArrayHelper::map(\app\models\VehicleType::find()->all(), 'id', 'type'),
            [
                'id' => 'typeVehChk',
                'prompt' => 'Выберите',
            ])
        ?>

        <div id="typeload" hidden>
            <?=
            $form->field($order, 'id_loadingTypes', ['options'=>[
                'id' => 'loadingtype',
            ]])->checkboxList([]);
            //        ]])->checkboxList(ArrayHelper::map(\app\models\LoadingType::find()->all(), 'id', 'type'));
            ?>

            <div id="longlength" hidden>
                <?php $order->longlenth = 0;?>
                <?= $form->field($order, 'longlenth', ['options' => [
                        'id'=>'longlenradio'
                ]])->radioList(['0'=>'Нет', '1' => 'Да']); ?>

                <div id="typebody" hidden>
                    <?= $form->field($order, 'id_bodyTypes', ['options'=>[
                        'id' => 'bodytype',
                        ]])->checkboxList([]);?>
                    <br>
                    <div id="size" hidden>

                        <p>Размеры (необязательно):</p>
                        <a onclick="$('#dimensions').slideToggle('slow');" href="javascript://">Необходимые размеры кузова (в метрах)</a>

                        <div id="dimensions" style="display: none;">
                            <?= $form->field($order, 'longer')->hint('Длинна')->label(false);?>
                            <?= $form->field($order, 'width')->hint('Ширина')->label(false); ?>
                            <?= $form->field($order, 'height')->hint('Высота')->label(false); ?>
                        </div>

                        <br>
                        <a onclick="$('#volume').slideToggle('slow');" href="javascript://">Необходимый объем кузова (м3)</a>

                        <div id="volume" style="display: none;">
                            <?= $form->field($order, 'volume')->hint('Объем')->label(false);?>

                        </div>
                        <br>
                        <a onclick="$('#pallet').slideToggle('slow');" href="javascript://">Количество поддонов(шт)</a>
                        <div id="pallet" style="display: none;">
                            <?= $form->field($order, 'ep', [inputOptions =>
                                ['id'=>'palletEP',
                                ]])
                                ->label(false)->hiddenInput();?>
                            <?= $form->field($order, 'rp', [inputOptions =>
                                ['id'=>'palletRP',
                                ]])
                                ->label(false)->hiddenInput();?>
                            <?= $form->field($order, 'lp', [inputOptions =>
                                ['id'=>'palletLP',
                                ]])
                                ->label(false)->hiddenInput();?>
                            <?= $form->field($order, 'type_pallet',['options' => [
                                'id'=>'palletradio'
                            ]])->radioList([
                                    '1'=>'"Евро"(1,2м * 0,8м)',
                                    '2'=>'"Русский"(1,2м * 1м)',
                                    '3'=>'"Финский"(1,2м * 1,2м)'
                                ])->label(false);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div id="route" hidden >
        <?= $form->field($route, 'routeStart', [inputOptions => ['id'=>'rStart']])?>
        <div id="hiddenRoutes">
            <ul>
                <?= $form->field($route, 'route1', [inputOptions => ['id'=>'r1']])->label('Промежуточные точки')->hiddenInput();?>
                <?= $form->field($route, 'route2', [inputOptions => ['id'=>'r2']])->label(false)->hiddenInput();?>
                <?= $form->field($route, 'route2', [inputOptions => ['id'=>'r3']])->label(false)->hiddenInput();?>
                <?= $form->field($route, 'route2', [inputOptions => ['id'=>'r4']])->label(false)->hiddenInput();?>
                <?= $form->field($route, 'route2', [inputOptions => ['id'=>'r5']])->label(false)->hiddenInput();?>
                <?= $form->field($route, 'route2', [inputOptions => ['id'=>'r6']])->label(false)->hiddenInput();?>
                <?= $form->field($route, 'route2', [inputOptions => ['id'=>'r7']])->label(false)->hiddenInput();?>
                <?= $form->field($route, 'route2', [inputOptions => ['id'=>'r8']])->label(false)->hiddenInput();?>
            </ul>
        </div>
        <?= $form->field($route, 'routeFinish', [inputOptions => ['id'=>'rFinish']]);?>
        <?= Html::button('Пересчитать', ['id' => 'but'])?>
        <?= Html::button('Добавить промежуточную точку', ['id' => 'addPoint'])?>
        <?= Html::button('Очистить', ['id' => 'clearAllPoint'])?>
        <?= Html::button('Скрыть/показать карту', ['class' => 'extremum-click'])?>
        <br>
        <div>Общий пробег: <b id="len"></b> км</div>
        <?= $form->field($route, 'distance', [inputOptions => ['id'=>'lengthRoute']])->label(false);?>
        <br>
        <div id="map" class="extremum-slide" style="width: auto; height: 200px"></div>
        <br>
        <?= Html::button('Скрыть/показать карту', ['class' => 'extremum-click'])?>
    </div>

    <div id="date_time" >
        <?= $form->field($order, 'datetime_start')->widget(kartik\datetime\DateTimePicker::className(),[
            'options' => [
                'placeholder' => 'Ввод даты/времени...',
                'name' => 'Order[datetime_start]',
            ],
            'convertFormat' => true,
        //        'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
        //        'value'=> date("d.m.Y h:i",(integer) $order->datetime_start),
            'pluginOptions' => [
                'minuteStep'     => 10,
                'format' => 'yyyy-M-d H:i',
                'autoclose'=> true,
                'weekStart'=>1, //неделя начинается с понедельника
                'startDate' => date('Y-m-d H:i:s'),
                'todayBtn'=>true, //снизу кнопка "сегодня",
                'language' => 'ru',
            ],
            'pluginEvents' => [
                'changeDate' => "function(e){
                            $('#order-datetime_start_max').datetimepicker().val($('#order-datetime_start').datetimepicker().val());
                            $('#order-datetime_start_max').parent().datetimepicker('setStartDate', $('#order-datetime_start').datetimepicker().val()).datetimepicker('update');
                        }",
            ]

        ]);?>
        <?= $form->field($order, 'datetime_start_max')->widget(kartik\datetime\DateTimePicker::className(),[
            'options' => [
                'placeholder' => 'Ввод даты/времени...',
                'name' => 'Order[datetime_start_max]',
            ],
        //        'convertFormat' => true,
        //        'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
        //        'value'=> date("d.m.Y h:i",(integer) $model->addtime),
            'pluginOptions' => [
                'minuteStep'     => 10,
                'format' => 'yyyy-m-d H:ii',
                'autoclose'=>true,
                'weekStart'=>1, //неделя начинается с понедельника
        //            'startDate' => date('d-m-Y H:i'),
        //            'todayBtn'=>true, //снизу кнопка "сегодня",
                'language' => 'ru',
            ],
        //        'pluginEvents' => [
        //            'changeDate' => "function(e){
        //                    $('#order-datetime_start_max').datetimepicker('setStartDate');
        ////                 alert($('#order-datetime_start').datetimepicker('setStartDate'));
        //
        //                }",
        //        ]
        ]);?>
    </div>
<hr>
    <div id="rates_div" >
        <?= $form->field($order, 'id_rates', ['options' => [
            'id' => 'rates_chk',
        ]])
            ->checkboxList([])
            ->label("Варианты доставки: ");
        ?>
    </div>
<hr>
    <?=Html::submitButton('Создать', ['name'=>'button', 'class' => 'btn btn-primary', 'value' => '0']) ?>
<?php ActiveForm::end(); ?>
<hr>
<?php
function pr($var) {
    static $int=0;
    echo '<pre><b style="background: blue;padding: 1px 5px;">'.$int.'</b> ';
    var_dump($var);
    echo '</pre>';
    $int++;
}
//    $lt = \app\models\LoadingType::find()
//        ->joinWith(['vehicles' => function($query){
//        $query->joinWith(['vehicletype']);
//        },
//        ])
//        ->where(['vehicle_type.id' => 1])
//        ->asArray()->all();
//$lt = ArrayHelper::map($lt, 'id', 'type');
    pr($test);
?>


<script>
    $(document).ready(function () {



    })


</script>




