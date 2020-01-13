<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 19.03.2018
 * Time: 14:33
 */

namespace app\components\widgets;

use app\models\Order;
use Yii;
use app\models\Document;
use app\models\Invoice;
use yii\base\Widget;
use yii\bootstrap\Modal;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;
use app\models\TypePayment;
use kartik\datetime\DateTimePicker;


class FinishOrderOnlySumWidget extends Widget
{
    public $id_order;
    public $ToggleButton = [
        'label' => 'Сумма',
        'class' => 'btn btn-xs btn-info'
    ];
    public $header = 'Завершение заказа';
    public $action = '/logist/order/finish-only-sum';

    public function run(){
        if($this->id_order)
            self::finishOrder();
    }

    private function finishOrder(){
        Modal::begin([
            'header' => $this->header,
            'toggleButton' => $this->ToggleButton,
//            'id' => 'modal_' .  $this->id_order
        ]);

        $order = Order::findOne(['id' => $this->id_order]);
        $order->real_datetime_start = $order->datetime_start;
        $order->id_price_zone_real = $order->id_pricezone_for_vehicle;
        $order->real_km = $order->route->distance;
        $order->id_route_real = $order->id_route;
        $order->cost_finish = $order->CalculateAndPrintFinishCost(false, $order->getDiscount($order->id_user))['cost'];
        $order->cost_finish_vehicle = $order->CalculateAndPrintFinishCost(false, true)['cost'];
        $form = ActiveForm::begin([
            'id' => 'form_'. $this->id_order,
            'action' => [$this->action, 'id_order' => $this->id_order],
            ]);

        echo $form->field($order, 'real_datetime_start')->widget(DatePicker::class, [
            'dateFormat' => 'dd.MM.yyyy'
        ]);

        echo $form->field($order, 'type_payment')->radioList(TypePayment::getTypiesPaymentsArray(), ['encode' => false]);
        echo $form->field($order, 'cost_finish_vehicle')->input('tel', ['id' => 'cost', 'autocomplete' => false]);
        echo $form->field($order, 'cost_finish')->input('tel', ['id' => 'cost_vehicle', 'autocomplete' => false]);

        echo $form->field($order, 'id_price_zone_real')->hiddenInput()->label(false);
        echo $form->field($order, 'real_km')->hiddenInput()->label(false);
        echo $form->field($order, 'id_route_real')->hiddenInput()->label(false);

        echo Html::a('Отмена', '#', ['id' => 'close_button', 'class' => 'btn btm-warning']);
        echo Html::submitButton('Сохранить', ['class' => 'btn btn-success']);
        ActiveForm::end();

        Modal::end();
    }

}