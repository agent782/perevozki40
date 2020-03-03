<?php
namespace app\models;
use yii\base\Model;
use app\models\Order;

class Report extends Model{
    public $date1;
    public $date2;

    public function rules()
    {
        return [
            [['date1', 'date2'], 'date', 'format' => 'php: d.m.Y']
        ];
    }

    public function getReportOrders(){
        $Report = [
            'sum' => 0,
            'sum_cash' => 0,
            'sum_bank' => 0,
        ];

        $Order = Order::find()
            ->where(['>=', 'datetime_start', strtotime($this->date1 . ' 0:00')])
            ->andWhere(['<=', 'datetime_start', strtotime($this->date2. ' 23:59')])
            ->andWhere(['in', 'status',[Order::STATUS_CONFIRMED_VEHICLE, Order::STATUS_CONFIRMED_SET_SUM, Order::STATUS_CONFIRMED_CLIENT]])
//            ->select(['cost_finish', 'cost_finish_vehicle', 'type_payment', 'paid_status'])
            ->all()
        ;

        foreach ($Order as $order){
            $Report['sum'] += $order->cost_finish;
            if($order->type_payment == Payment::TYPE_BANK_TRANSFER){
                $Report['sum_bank'] += $order->cost_finish;
            } else {
                $Report['sum_cash'] += $order->cost_finish;
            }
        }
        return $Report;
    }

}