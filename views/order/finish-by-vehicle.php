<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 09.03.2019
 * Time: 11:24
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\datetime\DateTimePicker;
use app\models\Vehicle;
use app\models\PriceZone;
use app\components\widgets\ShowMessageWidget;
use app\models\Tip;
use app\models\Payment;
/* @var $this yii\web\View */
/* @var $modelOrder app\models\Order */
/* @var $BTypies array*/
/* @var $LTypies array*/
/* @var $VehicleAttributes array*/
$this->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU');
$this->registerJsFile('@web/js/route.js');
$this->title = 'Фактические данные по заказу №' . $modelOrder->id;
$this->registerJsFile('/js/order.js');

?>

<div class="order-finish container">

    <h3><?= Html::encode($this->title) ?></h3>
    <strong>
        Перед завершением заказа согласуйте результат с Клиентом по телефону во избежании недопонимания с его стороны,
        что может привести к задержке оплаты заказа, понижению вашего рейтинга и негативному влиянию на имидж нашего сервиса!
        Аккуратно заполняйте результаты поездки и проверяйте перед подтверждением.
        Обращаем внимание, что в результате Тариф не может измениться в меньшую сторону по сравнению с установленным при принятии заказа!
        Если тариф изменится в большую сторону, поясните коротко в комментариях Клиенту из-за чего...
    </strong>
    <br>
    <?php
    \yii\widgets\Pjax::begin(['id' => 'update']);
    $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
        'validationUrl' => '/order/validate-order',
    ]);
    ?>
    <div class="col-lg-8">
        <?= $this->render('/route/_form', ['route' => $realRoute, 'form' => $form])?>
    </div>
<br><br>
    <div class="col-lg-4">

        <?= $form->field($modelOrder, 'real_datetime_start',[
            'enableClientValidation' => true
        ])->widget(DateTimePicker::className(),[
//                'name' => 'dp_1',
                //        'type' => DateTimePicker::TYPE_INPUT,
                'options' => [
                    'placeholder' => 'Ввод даты/времени...',
                    'onchange' => '$("#order-valid_datetime").val($("#order-datetime_start").val())'
                ],
                'convertFormat' => true,
//                'value'=> date("d.m.Y H:i",time()),
                'pluginOptions' => [
                    'format' => 'dd.MM.yyyy H:i',
                    'autoclose'=>true,
                    'weekStart'=>1, //неделя начинается с понедельника
                    'startDate' => $modelOrder->create_at, //самая ранняя возможная дата
//                'endDate' => date('d.m.Y H:i',  time() + 60*60*24*30),
                    'todayBtn'=>true, //снизу кнопка "сегодня",

                ],
            ]
        )?>
        <?= $form->field($modelOrder, 'datetime_finish')
            ->widget(DateTimePicker::className(),[
                'name' => 'dp_3',
                //        'type' => DateTimePicker::TYPE_INPUT,
                'options' => ['placeholder' => 'Ввод даты/времени...'],
                'convertFormat' => true,

//                'value'=> '',
                'pluginOptions' => [
                    'format' => 'dd.MM.yyyy H:i',
                    'autoclose'=>true,
                    'weekStart'=>1, //неделя начинается с понедельника
//                    'startDate' => date($modelOrder->real_datetime_start), //самая ранняя возможная дата
//                'endDate' => date('d.m.Y H:i',  time() + 60*60*24*30),
                    'todayBtn'=>true, //снизу кнопка "сегодня"
                ]
            ])
        ?>

        <?php if($longlength) {
            echo $form->field($modelOrder, 'longlength')->radioList(['Нет', 'Да'], ['value' => 0])->label(
                'Груз длинномер ' . \app\components\widgets\ShowMessageWidget::widget([
                    'helpMessage' => Tip::findOne(['model' => 'Order','attribute' => 'longlength'])->description,
                ])
//                ,['encode' => true]
            );
        }
        ?>
        <?php
        foreach ($VehicleAttributes as $attribute){
            echo $form->field($modelOrder, $attribute, [
                'inputOptions' => [
                    'type' => 'tel',
                    'style' => 'width: 150px',
                ]
            ]);
        }
        ?>

        <?= $form->field($modelOrder, 'real_km')
            ->input('tel', ['id' => 'real_distance'])
            ->label('Реальный пробег')
        ;?>
        <div id="real_h_loading">
            <?= $form->field($modelOrder, 'real_h_loading')->input('tel')?>
        </div>
        <?php
            if($modelOrder->vehicle->hasLoadingType(Vehicle::LOADING_TYPE_OVERHAND || Vehicle::LOADING_TYPE_SIDEWAYS)){
                echo $form->field($modelOrder, 'real_remove_awning')->input('tel');
            }
        ?>
        <?= $form->field($modelOrder,'additional_cost')->input('tel')->label(
            'Дополнительные рассходы' . ShowMessageWidget::widget([
                'helpMessage' => Tip::findOne(['model' => 'Order', 'attribute' =>'additional_cost'])->description
            ])
        )
        ->hint('Обязательно распишите в комментариях на след. странице! Потребуются подтверждающие чеки.')?>
    </div>
<br><br>
    <div class="col-lg-11">
        <?=
        Html::a('Отмена', $redirect, ['class' => 'btn btn-warning'])
        ?>
        <?= Html::submitButton('Далее',
            [
                'class' => 'btn btn-success',
                'name' => 'button',
                'value' => 'next'
            ])?>
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
        });

        $('#lengthRoute').on('change', function () {
            $('#real_distance').val($(this).val()).trigger('change');
        });

        $('#real_distance').on('change', function () {
            if($(this).val()>=120){
                $('#real_h_loading').show();
            } else {
                $('#order-real_h_loading').val('');
                $('#real_h_loading').hide();
            }
        })
    });
</script>