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
use app\models\Vehicle;
use yii\helpers\ArrayHelper;

class ConsoleController extends Controller
{
    public function actionAutoFind($id_order){
        $order = Order::findOne($id_order);
        if(!$order) return 0;
        if(!$order->auto_find) return 0;
        $vehicles = Vehicle::find()->where(['in', 'id', $order->suitable_vehicles])->all();
        if (!$vehicles) return 0;
        foreach ($vehicles as $vehicle){
            $car_owner = $vehicle->profile;
//            if (ArrayHelper::isIn($car_owner->id, $order->alert_car_owner_ids)){
//
//            } else {
                echo $car_owner->id . '"\n"';
//            }
        }
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