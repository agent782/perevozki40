<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\datetime\DateTimePicker;
/* @var $this yii\web\View */
/* @var $model app\models\Order */
$this->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU');
$this->registerJsFile('@web/js/route.js');
$this->title = 'Изменение заказа №' . $modelOrder->id;
?>
<div class="order-update">

    <h4><?= Html::encode($this->title) ?></h4>

    <?php
    \yii\widgets\Pjax::begin(['id' => 'update']);
        $form = ActiveForm::begin([
            'validationUrl' => '/order/validate-order'
        ]);
    ?>
    <?= $form->field($modelOrder, 'datetime_start',[
        'enableClientValidation' => true
    ])->widget(DateTimePicker::className(),[
            'name' => 'dp_1',
            //        'type' => DateTimePicker::TYPE_INPUT,
            'options' => [
                'placeholder' => 'Ввод даты/времени...',
                'onchange' => '$("#order-valid_datetime").val($("#order-datetime_start").val())'
            ],
            'convertFormat' => true,
            'value'=> date("d.m.Y H:i",time()),
            'pluginOptions' => [
                'format' => 'dd.MM.yyyy H:i',
                'autoclose'=>true,
                'weekStart'=>1, //неделя начинается с понедельника
                'startDate' => date('d.m.Y H:i',  time() + 60*60), //самая ранняя возможная дата
//                'endDate' => date('d.m.Y H:i',  time() + 60*60*24*30),
                'todayBtn'=>true, //снизу кнопка "сегодня",

            ],
        ]
    )?>
    <?= $form->field($modelOrder, 'valid_datetime')
        ->widget(DateTimePicker::className(),[
            'name' => 'dp_2',
            //        'type' => DateTimePicker::TYPE_INPUT,
            'options' => ['placeholder' => 'Ввод даты/времени...'],
            'convertFormat' => true,

            'value'=> date("d.m.Y H:i",time()),
            'pluginOptions' => [
                'format' => 'dd.MM.yyyy H:i',
                'autoclose'=>true,
                'weekStart'=>1, //неделя начинается с понедельника
                'startDate' => date('d.m.Y H:i',  time() + 60*10), //самая ранняя возможная дата
//                'endDate' => date('d.m.Y H:i',  time() + 60*60*24*30),
                'todayBtn'=>true, //снизу кнопка "сегодня"
            ]
        ])
    ?>

    <?= $form->field($modelOrder, 'type_payment')->radioList($TypiesPayment, [
        'onchange' => '
            if($(this).find("input:checked").val()  == 3) {
                $("#companies").show();    
            } else{
                $("#companies").hide();
                $("#companies").find("input").each(function(){
                    $(this).prop("checked", false);
                });            };
        '
    ])?>

    <div id="companies" hidden>
        <?= $form->field($modelOrder, 'id_company',[
            'enableAjaxValidation' => true,
        ])->radioList($companies)?>
    </div>
</div>

<div id="route"  class="container" >
    <h4>Маршрут.</h4>
    <br>
    <?= $form->field($route, 'routeStart', ['inputOptions' => [
        'id'=>'rStart',
        'class' => 'points col-xs-12'
    ]])?>
    <br>
    <div id="hiddenRoutes">
        <ul>
            <?= $form->field($route, 'route1', ['inputOptions' => ['id'=>'r1','class' => 'points col-xs-12']])->label('Промежуточные точки')->hiddenInput();?>
            <?= $form->field($route, 'route2', ['inputOptions' => ['id'=>'r2','class' => 'points col-xs-12']])->label(false)->hiddenInput();?>
            <?= $form->field($route, 'route3', ['inputOptions' => ['id'=>'r3','class' => 'points col-xs-12']])->label(false)->hiddenInput();?>
            <?= $form->field($route, 'route4', ['inputOptions' => ['id'=>'r4','class' => 'points col-xs-12']])->label(false)->hiddenInput();?>
            <?= $form->field($route, 'route5', ['inputOptions' => ['id'=>'r5','class' => 'points col-xs-12']])->label(false)->hiddenInput();?>
            <?= $form->field($route, 'route6', ['inputOptions' => ['id'=>'r6','class' => 'points col-xs-12']])->label(false)->hiddenInput();?>
            <?= $form->field($route, 'route7', ['inputOptions' => ['id'=>'r7','class' => 'points col-xs-12']])->label(false)->hiddenInput();?>
            <?= $form->field($route, 'route8', ['inputOptions' => ['id'=>'r8','class' => 'points col-xs-12']])->label(false)->hiddenInput();?>
        </ul>
    </div>
    <br>
    <?= $form->field($route, 'routeFinish', ['inputOptions' => ['id'=>'rFinish','class' => 'points col-xs-12']]);?>
    <br>
    <?= Html::button('Пересчитать', ['id' => 'but'])?>

    <?= Html::button('Добавить промежуточную точку', ['id' => 'addPoint'])?>

    <?= Html::button('Очистить', ['id' => 'clearAllPoint'])?>

    <?= Html::button('Скрыть/показать карту', ['class' => 'extremum-click'])?>
    <br>

    <h4><div>Приблизительный пробег*: <b id="len" class="h3"></b> км</div></h4>

    <?= $form->field($route, 'distance', ['inputOptions' => [
        'id'=>'lengthRoute',
    ]])->label(false)->hiddenInput();?>

    <br>
    <div id="map" class="extremum-slide" style="width: auto; height: 200px"></div>

    <br>
    <div class="col-lg-12">
        <?=
        Html::a('Отмена', '/order/client', ['class' => 'btn btn-warning'])
        ?>

        <?= Html::submitButton('Далее', [
            'class' => 'btn btn-success',
            'name' => 'button',
            'value' => 'next4'
        ])?>
    </div>
</div>

<div class="col-lg-5">


    <?php
        ActiveForm::end();
        \yii\widgets\Pjax::end();
    ?>

</div>
<script type="text/javascript">
    //Нет submit формы при нажатии enter
    $(function () {
        $('.points').keypress(function (event) {
            if (event.which == '13') {
                event.preventDefault();
            }
        })
    });
</script>