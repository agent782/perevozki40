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
$this->title = 'Информация о заказе.';
$this->registerJsFile('/js/order.js');

?>

<div class="order-finish container">

    <h3><?= Html::encode($this->title) ?></h3>

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
        <?= $form->field($modelOrder, 'id_vehicle_type')->hiddenInput()->label(false)?>
        <?php
//        if(is_array($modelOrder->body_typies)
//            && array_key_exists(0, $modelOrder->body_typies)
//            && $modelOrder->id_vehicle_type == Vehicle::TYPE_SPEC
//        ){
            echo $form->field($modelOrder, 'body_typies')->checkboxList([
                    $modelOrder->body_typies[1] => $modelOrder->body_typies[1]
                ], ['hidden' => true])->label(false);
//        }
        ?>


        <?= $form->field($modelOrder, 'datetime_start',[
            'enableClientValidation' => true
        ])->widget(DateTimePicker::className(),[
                'name' => 'dp_1',
//                'type' => DateTimePicker::TYPE_INPUT,
                'options' => [
                    'placeholder' => 'Ввод даты/времени...',
                    'onchange' => '$("#order-valid_datetime").val($("#order-datetime_start").val())'
                ],
                'convertFormat' => true,
//                'value'=> date("d.m.Y H:i",time()),
                'pluginOptions' => [
                    'zIndexOffset' => 10,
                    'format' => 'dd.MM.yyyy H:i',
                    'autoclose'=>true,
                    'weekStart'=>1, //неделя начинается с понедельника
//                    'startDate' => $modelOrder->create_at, //самая ранняя возможная дата
//                'endDate' => date('d.m.Y H:i',  time() + 60*60*24*30),
                    'todayBtn'=>true, //снизу кнопка "сегодня",

                ],
            ]
        )?>

        <?php
        foreach ($VehicleAttributes as $attribute){
            echo $form->field($modelOrder, $attribute, [
                'inputOptions' => [
//                    'type' => 'tel',
                    'style' => 'width: 150px',
                ]
            ]);
        }
        ?>
        <?php if($longlength) {
            echo $form->field($modelOrder, 'longlength')->radioList(['Нет', 'Да'])->label(
                'Груз длинномер ' . \app\components\widgets\ShowMessageWidget::widget([
                    'helpMessage' => Tip::findOne(['model' => 'Order','attribute' => 'longlength'])->description,
                ])
            );
        }
        ?>
        <?= $form->field($modelOrder, 'type_payment')->radioList($TypiesPayment, ['encode' => false])?>
        <?= $form->field($modelOrder, 'comment')->textarea([
            'placeholder' => 'Заказчик, телефон, Организация ...'
        ])->label('Информация о Клиенте')?>

    </div>
<br><br>
    <div class="col-lg-11">
        <?=
        Html::a('Отмена', $redirect, ['class' => 'btn btn-warning'])
        ?>
        <?= Html::submitButton('Зарегистрировать',
            [
                'class' => 'btn btn-success',
                'name' => 'button',
                'value' => 'next2'
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