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

<div class="order-finish container">

    <h3><?= Html::encode($this->title) ?></h3>
    <br>
    <?php
    \yii\widgets\Pjax::begin(['id' => 'update']);
    $form = ActiveForm::begin()
    ?>
    <div class="col-lg-4">
        <strong>Тарифная зона после изменения данных по заказу: </strong>
        <?= $finishCostText?>
        <strong>
            <p>Способ оплаты: <?= $modelOrder->paymentText?></p>
            <?php
                if(!$modelOrder->cost) {
                    echo $form->field($modelOrder, 'hand_vehicle_cost')->input('tel')
                        ->label('Сумма к оплате Клиентом водителю:');
                }
            ?>

        </strong>
       <br><br>
        <i>Тарифная зона при принятии заказа: </i>
        <i>
            <?=
            PriceZone::findOne($modelOrder->id_pricezone_for_vehicle)
                ->getWithDiscount(\app\models\setting\SettingVehicle::find()->limit(1)->one()->price_for_vehicle_procent)
                ->getTextWithShowMessageButton($modelOrder->route->distance);
            ?>
        </i>
        <br><br>

    </div>
    <div class="col-lg-4">
        <strong>Завершенный заказ.<br><br>
        <?= $modelOrder->getFullFinishInfo(true, $realRoute,true, false)?></strong>
    </div>
    <div class="col-lg-4">
        <i>Первоначальный заказ.<br><br>
        <?= $modelOrder->getFullNewInfo(true,true, false)?></i>
    </div>

    <div class="col-lg-11">
        <?php if ($modelOrder->type_payment !== Payment::TYPE_CASH){
            echo $form->field($modelOrder, 'ClientPaidCash')->checkbox(['label' => 'Клиент оплатил наличными или на банковскую карту']);
    }?>
    </div>
    <div class="col-lg-11">
        <?=
        Html::submitButton('Назад', [
            'class' => 'btn btn-warning',
            'name' => 'button',
            'value' => 'next'
        ])
        ?>
        <?= Html::submitButton('Подтвердить',
            [
                'class' => 'btn btn-success',
                'name' => 'button',
                'value' => 'finish',
                'data-confirm' => Yii::t('yii'
                    , 'После завершения заказа внести изменения будет нельзя! Завершить заказ?'),
                'data-method' => 'post'
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