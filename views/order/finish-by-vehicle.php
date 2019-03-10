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

<div class="order-update container">

    <h3><?= Html::encode($this->title) ?></h3>
    <br>
    <?php
    \yii\widgets\Pjax::begin(['id' => 'update']);
    $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
        'validationUrl' => '/order/validate-order',
    ]);
    ?>

    <div class="col-lg-4">
        <?= $form->field($modelOrder, 'paid_status')->radioList([
            $modelOrder::PAID_YES => 'Да',
            $modelOrder::PAID_NO => 'Нет'
        ])->label('Заказчик оплатил заказ?');?>
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

//                'value'=> date("d.m.Y H:i",time()),
                'pluginOptions' => [
                    'format' => 'dd.MM.yyyy H:i',
                    'autoclose'=>true,
                    'weekStart'=>1, //неделя начинается с понедельника
//                    'startDate' => date('d.m.Y H:i',  time()), //самая ранняя возможная дата
//                'endDate' => date('d.m.Y H:i',  time() + 60*60*24*30),
                    'todayBtn'=>true, //снизу кнопка "сегодня"
                ]
            ])
        ?>

        <?php
        if($LTypies){
            echo $form->field($modelOrder, 'loading_typies')->checkboxList(ArrayHelper::map($LTypies, 'id', 'type'),
                [
                    'id' => 'chkLoadingTypies',
                ])->label('Необходимый тип погрузки/выгрузки.')
//                ->hint('Выбирайте дополнительные типы погрузки только при необходимости!')
            ;
        }
        ?>

        <?php if($modelOrder->id_vehicle_type == \app\models\Vehicle::TYPE_TRUCK) {
            echo $form->field($modelOrder, 'longlength')->radioList(['Нет', 'Да'], ['value' => 0])->label(
                'Груз длинномер ' . \app\components\widgets\ShowMessageWidget::widget([
                    'helpMessage' => \app\models\Tip::findOne(['model' => 'Order','attribute' => 'longlength'])->description,
                    'ToggleButton' => ['label' => '<img src="/img/icons/help-25.png">', 'class' => 'btn'],
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
        <?= $form->field($modelOrder, 'real_h')->input('tel')?>
        <?= $form->field($modelOrder, 'comment_vehicle')->textarea();?>
    </div>
    <div class="col-lg-8">
        <?= $this->render('/route/_form', ['route' => $realRoute, 'form' => $form])?>
    </div>
    <div class="col-lg-11">
        <?=
        Html::a('Отмена', $redirect, ['class' => 'btn btn-warning'])
        ?>
        <?= Html::submitButton('Далее',
            [
                'class' => 'btn btn-success',
                'name' => 'button',
                'value' => 'update'
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
            $('#real_distance').val($(this).val());
//            alert($('#lengthRoute').val());
        })
    });
</script>