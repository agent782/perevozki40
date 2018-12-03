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
?>

<h4>Шаг 5 из 5.</h4>
<div class="container-fluid">
<?php
    $form = ActiveForm::begin([
        'validationUrl' => '/order/validate-order'
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
                'format' => 'd.M.yyyy H:i',
                'autoclose'=>true,
                'weekStart'=>1, //неделя начинается с понедельника
                'startDate' => date('d.m.Y H:i',  time()), //самая ранняя возможная дата
                'todayBtn'=>true, //снизу кнопка "сегодня",

            ],
        ]
    )?>
    <?= $form->field($modelOrder, 'valid_datetime')
        ->widget(DateTimePicker::className(),[
            'name' => 'dp_2',
            //        'type' => DateTimePicker::TYPE_INPUT,
            'options' => ['placeholder' => 'Ввод даты/времени...'],
            'convertFormat' => false,

            'value'=> date("d.m.Y H:i",time()),
            'pluginOptions' => [
                'format' => 'dd.mm.yyyy H:i',
                'autoclose'=>true,
                'weekStart'=>1, //неделя начинается с понедельника
                'startDate' => date('d.m.Y H:i',  time()), //самая ранняя возможная дата
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
                $("#companies").find("input").removeAttr("checked");
            };
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
            'item' => function ($index, $label, $name, $checked, $value) use ($route){
                $PriceZone = PriceZone::find()->where(['id' => $value])->one();
                $return = '<label>';
                $return .= '<input type="checkbox" name="' . $name . '"' . 'value="' . $value . '"' . ' >' . "\n";
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
?>
</div>