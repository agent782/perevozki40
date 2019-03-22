<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 18.10.2018
 * Time: 10:28
 */
//var_dump($route);
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use kartik\datetime\DateTimePicker;
use app\models\PriceZone;
use app\components\widgets\ShowMessageWidget;
//echo date('d.m.Y H:i');
var_dump($modelOrder->getDiscount(Yii::$app->user->id))
?>

<h4>Шаг 5 из 5.</h4>
<div class="container-fluid">
    <?php \yii\widgets\Pjax::begin(['id' => 'create5']);?>
<?php

    $form = ActiveForm::begin([
        'validationUrl' => '/order/validate-order',
        'options' => [
            'data-pjax' => true
        ]

    ]);

?>

<div class="col-lg-5">
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
        'id' => 'type_payment',
        'onchange' => '
            if($(this).find("input:checked").val()  == 3) {
                $("#companies").show();  
                  $("#companies").removeAttr("checked");
            } else{
                $("#companies").hide();
                $("#companies").find("input").each(function(){
                    $(this).prop("checked", false);
                });
            }
            changePriceZones();
        '
    ])?>

    <div id="companies" hidden>
    <?= $form->field($modelOrder, 'id_company',[
        'enableAjaxValidation' => true,
    ])->radioList($companies)?>
    </div>
    </div>
    <div class="col-lg-5">

    <?= $form->field($modelOrder, 'selected_rates[]')->label('Выберите подходящие тарифы *.')
        ->checkboxList($modelOrder->suitable_rates, [
            'id' => 'selected_rates',
            'encode' => false
        ]);
    ?>
    </div>


<div class="col-lg-12">
    <?=
        Html::a('Отмена', '/order/client', ['class' => 'btn btn-warning'])
    ?>

    <?= Html::submitButton('Оформить заказ', [
        'class' => 'btn btn-success',
        'name' => 'button',
        'value' => 'next5'
    ])?>
</div>
<?php
    ActiveForm::end();
\yii\widgets\Pjax::end();
?>
</div>
<script>
    function changePriceZones() {
        var type_payment = $('#type_payment').find("input:checked").val();
        var datetime_start = $('#order-datetime_start').val();
        var valid_datetime = $('#order-valid_datetime').val();
        $.pjax.reload({
            container:"#create5",
//            dataType:"JSON",
            type:"POST", data:{
                "type_payment":type_payment,
                "datetime_start":datetime_start,
                "valid_datetime":valid_datetime
            }});
    }
</script>
