<?php
/* @var \app\models\Order $modelOrder*/
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
use yii\helpers\Url;
use app\models\PriceZone;
use app\components\widgets\ShowMessageWidget;
use app\models\Profile;
use kartik\icons\Icon;
use yii\widgets\Pjax;

//echo date('d.m.Y H:i');
Icon::map($this);
?>

<h4>Шаг 5 из 5.</h4>
<div class="container-fluid">
<?php

    $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
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
            'pluginEvents' => [
                'changeDate' => "
                    function(e){
                        changePriceZones();
                    }
                "
            ]

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

    <input value="<?= $user_id?>" hidden disabled>

    <?= $form->field($modelOrder, 'type_payment', [
        'errorOptions' => [
            'class' => 'help-block' ,
            'encode' => false
        ]
    ])->radioList($TypiesPayment, [
        'id' => 'type_payment',
        'encode' => false,
        'onchange' => '
            if($(this).find("input:checked").val()  == 3 
            ) {
                $("#companies").show();  
                  $("#companies").removeAttr("checked");
            } else{
                $("#companies").hide();
                $("#companies").find("input").each(function(){
                    $(this).prop("checked", false);
                });
            }
//            $("#rates").html("");
            changePriceZones();
        '
    ])->label('Способ оплаты' . \app\models\Tip::getTipButtonModal($modelOrder, 'type_payment'))?>
    <?php
        $companiesHide = ($modelOrder->type_payment == \app\models\Payment::TYPE_BANK_TRANSFER)
            ? '' : 'hidden';
    ?>
    <div id="companies" <?= $companiesHide?> >
        <?php
            $link_add_company = (Yii::$app->user->isGuest)
                ? ''
                : Html::a(Html::icon('plus', [
                'class' => 'btn btn-info',
                'title' => 'Добавить юр. лицо'
            ]), Url::to(['/company/create',
                'user_id' => $user_id,
                'redirect' => Url::to([
                    '/order/create',
                    'user_id' => $user_id,
                    'redirect' => $redirect,
                ])
            ]), ['target' => '_blank'])
        ;
        ?>

    <?php if(Profile::notAdminOrDispetcher()) {
            echo $form->field($modelOrder, 'id_company', [
                'enableAjaxValidation' => true,
            ])->radioList($companies)->label('Юр. лица: ' . $link_add_company);
    }
    ?>
    </div>
    </div>
    <div class="col-lg-5">

        <label>Маршрут.</label>
        <comment>
            <?= $route->fullRoute?>
        </comment>

        <?php Pjax::begin(['id' => 'create5']);?>
<!--        <div id="rates">-->
    <?= $form->field($modelOrder, 'selected_rates')->label('Выберите подходящие по стоимости тарифы *.')
        ->checkboxList($modelOrder->suitable_rates, [
            'id' => 'selected_rates',
            'encode' => false
        ])
        ->hint('Стоимость указана минимальная, рассчитана на основании указанного Вами маршрута и характеристик груза.
        . На нее могут повлиять: 
        изменение фактического маршрута, изменение характеристик груза, время погрузки/выгрузки,
        "растентовка" сбоку/сверху, дополнительные расходы (платные дороги, платные въезды,
        услуги грузчика и т.п.)'
    );
    ?>
<!--        </div>-->
        <?php
            Pjax::end();
        ?>
        <comment>
            Чем больше тарифов выбрано, тем больше ТС подойдут под Ваш заказ.
        </comment>
        <br><br>
    </div>

<div class="col-lg-12">
    <?= Html::a('Назад', \yii\helpers\Url::previous(), ['class' => 'btn btn-warning'])?>
    <?=
    (!Yii::$app->user->isGuest)
        ? Html::submitButton('Оформить заказ', [
        'class' => 'btn btn-success',
        'name' => 'button',
        'value' => 'next5' ,
//        'onclick' => new \yii\web\JsExpression('
//            alert ("Заказ оформлен");
//        ')
    ])
        : 'Для заказа услуг он-лайн необходимо '
            . Html::a('ВОЙТИ ИЛИ ЗАРЕГИСТРИРОВАТЬСЯ.', '/default/login', ['class' => 'btn btn-info'])
    ?>
    <p class="h4">Остались вопросы? <a href="tel:+74843990949">Позвоните нам!</a> </p>

</div>
<?php
    ActiveForm::end();
?>
</div>
<script>
    function changePriceZones() {
        startLoading();
        var type_payment = $('#type_payment').find("input:checked").val();
        var datetime_start = $('#order-datetime_start').val();
        var valid_datetime = $('#order-valid_datetime').val();
        $.pjax.reload({
            container: "#create5",
//            dataType:"JSON",
            type: "POST",
            data: {
                "type_payment": type_payment,
                "datetime_start": datetime_start,
                "valid_datetime": valid_datetime
            }
        });

        $(document).on('pjax:success', function(event, data, status, xhr, options) {
            endLoading();
        });
    }

</script>
