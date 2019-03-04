<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\datetime\DateTimePicker;
use app\models\Vehicle;
use app\models\PriceZone;
use app\components\widgets\ShowMessageWidget;
/* @var $this yii\web\View */
/* @var $modelOrder app\models\Order */
/* @var $BTypies array*/
/* @var $LTypies array*/
/* @var $VehicleAttributes array*/
$this->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU');
$this->registerJsFile('@web/js/route.js');
var_dump($modelOrder->selected_rates);
$this->title = 'Изменение заказа №' . $modelOrder->id;
?>

<div class="order-update">

    <h3><?= Html::encode($this->title) ?></h3>
    <br>
    <?php
    \yii\widgets\Pjax::begin(['id' => 'update']);
        $form = ActiveForm::begin([
            'validationUrl' => '/order/validate-order'
        ]);
    ?>

    <div class="col-lg-4">

        <?= $form->field($modelOrder, 'selected_rates')->label('Выберите подходящие тарифы *.')
            ->checkboxList($modelOrder->suitable_rates, [
                'item' => function ($index, $label, $name, $checked, $value) use ($route){
                    $PriceZone = PriceZone::find()->where(['id' => $value])->one();
                    $return = '<label>';
                    $chk = ($checked)?'checked =""':' ';
                    $return .= '<input type="checkbox" name="' . $name . '"' . 'value="' . $value . '"' . $chk . ' >' . "\n";
                    $return .= '<i class="fa fa-square-o fa-2x"></i>' . "\n" .
                        '<i class="fa fa-check-square-o fa-2x"></i>' . "\n";
//                $return .= '<span>' . ucwords($label) . '</span>' . "\n";
                    $return .= ' &asymp; ' . $PriceZone->CostCalculation($route->distance);
                    $return .= ' руб.* ';
                    $return .= '</label>'
                        .  ShowMessageWidget::widget([
                            'helpMessage' => $PriceZone->printHtml(),
                            'header' => 'Тарифная зона ' . $value,
                            'ToggleButton' => ['label' => '<img src="/img/icons/help-25.png">', 'class' => 'btn'],
                        ])
                        . '</label><br>'
                    ;

                    return $return;
                }
            ]);
        ?>    </div>

    <div class="col-lg-4">

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
    <div class="col-lg-4">
        <?php if($modelOrder->id_vehicle_type == \app\models\Vehicle::TYPE_TRUCK) {
            echo $form->field($modelOrder, 'longlength')->radioList(['Нет', 'Да'], ['value' => 0])->label(
                'Груз длинномер ' . \app\components\widgets\ShowMessageWidget::widget([
                    'helpMessage' => '',
                    'ToggleButton' => ['label' => '<img src="/img/icons/help-25.png">', 'class' => 'btn'],
                ])
            );
            echo $form->field($modelOrder, 'cargo')->textarea([
                'placeholder' => '20 коробок 30х30х30см....Холодильник........Станок 1,5 х 1,5 х 1,5м'
            ]);

        }
        if($modelOrder->id_vehicle_type == \app\models\Vehicle::TYPE_PASSENGER){
            echo $form->field($modelOrder, 'cargo')->textarea([
                'placeholder' => 'Детская коляска'
            ]);
        }
        if($modelOrder->id_vehicle_type == \app\models\Vehicle::TYPE_SPEC){
            echo $form->field($modelOrder, 'cargo')->textarea()->label('Описание работ');
        }

        ?>
        <?php
        foreach ($VehicleAttributes as $attribute){
            echo $form->field($modelOrder, $attribute, [
                'inputOptions' => [
                    'type' => 'tel',
                    'style' => 'width: 150px'
                ]
            ]);
        }
        ?>

    </div>
    <div class="col-lg-10">
        <?= $this->render('/route/_form', ['route' => $route, 'form' => $form])?>
    </div>


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