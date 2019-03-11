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
$this->title = 'Фактические данные по заказу №' . $modelOrder->id;

//var_dump($modelOrder);
//echo '<br><br>';
//var_dump($realRoute);
?>

<div class="order-update container">

    <h3><?= Html::encode($this->title) ?></h3>
    <br>
    <?php
    \yii\widgets\Pjax::begin(['id' => 'update']);
    $form = ActiveForm::begin()
    ?>
    <?= $modelOrder->real_tonnage;?>
    <div class="col-lg-4">
        <?= $form->field($modelOrder, 'paid_status')->radioList([
            $modelOrder::PAID_YES => 'Нет',
            $modelOrder::PAID_NO => 'Да'
        ])->label('Заказчик оплатил заказ?');?>

        <p>Тарифная зона при принятии заказа: </p>
        <p>
            <?=
                PriceZone::findOne($modelOrder->id_pricezone_for_vehicle)
                    ->getTextWithShowMessageButton($modelOrder->route->distance);
            ?>
        </p>
        <?php if($modelOrder->id_pricezone_for_vehicle != $modelOrder->id_price_zone_real
            || $modelOrder->id_route != $modelOrder->id_route_real):?>
        <p>Тарифная зона после изменения данных по заказу: </p>
        <p>
            <?=
            PriceZone::findOne($modelOrder->id_price_zone_real)
                ->getTextWithShowMessageButton($modelOrder->realRoute->distance);
            ?>
        </p>
        <?php endif;?>

    </div>
    <div class="col-lg-8">

    </div>
    <div class="col-lg-11">
        <?=
        Html::submitButton('Назад', [
            'class' => 'btn btn-warning',
            'name' => 'button',
            'value' => 'next'
        ])
        ?>
        <?= Html::submitButton('Далее',
            [
                'class' => 'btn btn-success',
                'name' => 'button',
                'value' => 'finish'
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