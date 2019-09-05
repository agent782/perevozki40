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
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use yii\widgets\MaskedInput;
use app\models\TypePayment;


class FinishOrderOnlySumWidget extends Widget
{
    public $id_order;
    public $ToggleButton = [
        'label' => 'Сумма',
        'class' => 'btn btn-xs btn-info'
    ];
    public $header = 'Завершение заказа';
    public $action = '/logist/finishOnlySum';

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
        $form = ActiveForm::begin([
            'id' => 'form_'. $this->id_order,
            'action' => $this->action,
            ]);
        echo $form->field($order, 'type_payment')->radioList(TypePayment::getTypiesPaymentsArray(), ['encode' => false]);
        echo $form->field($order, 'cost_finish');
        echo $form->field($order, 'cost_finish_vehicle');
        ActiveForm::end();

        Modal::end();
    }
}