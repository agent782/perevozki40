<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 14.07.2020
 * Time: 15:54
 */

namespace app\commands;


use app\models\Message;
use app\models\Order;
use yii\console\Controller;

class ConsoleController extends Controller
{
    public function actionAutoFind(){
        $mes = new Message([
            'id_to_user' => 1,
            'title' => 'Проверка'
        ]);
        $mes->sendPush(false);
        sleep(5);
        $mes->sendPush(false);
        sleep(5);
        $mes->sendPush(false);
    }

    public function actionSetSuitableVehiclesIds($id_order, $sort = true){
        $order = Order::findOne($id_order);

        if(!$order) return false;

        if($order->status != Order::STATUS_NEW && $order->status != Order::STATUS_IN_PROCCESSING) return false;

        $vehicles = ($sort)
            ? $order->getSortSuitableVehicles(false)
            : $order->getSuitableVehicles(false);
        $res = [];
//        $return = '';
        foreach ($vehicles as $vehicle){
            $res[] = $vehicle->id;
//            echo $vehicle->id . "\n";
        }
        $order->suitable_vehicles = $res;
        $order->save(false);
    }

}