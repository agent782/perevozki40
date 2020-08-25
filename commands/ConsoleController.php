<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 14.07.2020
 * Time: 15:54
 */

namespace app\commands;

use app\models\CalendarVehicle;
use app\models\Message;
use app\models\Order;
use app\models\Profile;
use yii\console\Controller;
use app\models\Vehicle;
use yii\helpers\ArrayHelper;

class ConsoleController extends Controller
{
    public function actionAutoFind($id_order)
    {
        $order = Order::findOne($id_order);
        if (!$order) return 0;

        $count = 0;
        while (!$order->suitable_vehicles && $count < 120){
            if(!$order->auto_find) return;
            sleep(5);
            $count++;
        }
        if (!$order->auto_find || !$order->suitable_vehicles) return 0;
        $car_owners = [];
        foreach ($order->suitable_vehicles as $id_vehicle) {
            $vehicle = Vehicle::findOne($id_vehicle);
            if (!in_array($vehicle->id_user, $car_owners)
                && $vehicle->hasOrderOnDate($order->datetime_start) <= 140
            ) {
                $calendar = $vehicle->getCalendarVehicle($order->datetime_start)->one();
                if($calendar && $calendar->status == CalendarVehicle::STATUS_BUSY){
                    continue;
                }

                $car_owners[] = $vehicle->id_user;
            }
        }
        if (!$car_owners) return 0;
        foreach ($car_owners as $car_owner_id) {
            if ($order->alert_car_owner_ids
                && ArrayHelper::keyExists($car_owner_id, $order->alert_car_owner_ids)
            ) {
                continue;
            } else {
                if (!in_array($order->status, [Order::STATUS_NEW, Order::STATUS_IN_PROCCESSING])) {
                    $order->auto_find = false;
                    $order->save(false);
                }
                if (!$order->auto_find) return 'STOP';

                $sleep = 600 / count($car_owners);
//                $has_alert = Message::find()
//                    ->where(['id_order' => $id_order])
//                    ->andWhere(['id_to_user' => $car_owner_id])
//                    ->andWhere(['type' => Message::TYPE_ALERT_CAR_OWNER_NEW_ORDER])
//                    ->count();

                $mes = new Message();

                $mes->create_at = date('d.m.Y H:i', time());

                $mes->AlertNewOrder($car_owner_id, $id_order);
//                $mes->AlertNewOrder(1, $id_order);
                $arr = $order->alert_car_owner_ids;
                $arr[$car_owner_id] = time();
                $order->alert_car_owner_ids = $arr;
                $order->save(false);

                $seconds = 0;
                while ($seconds <= $sleep ){
                    if(!$order->auto_find) break;
                    sleep(1);
                    $seconds++;
                }
                // только для тестирования в консоли
//                echo $car_owner_id . "\n";

            }
        }
        $order->auto_find = false;
        $order->save(false);
    }

    public function actionSetSuitableVehiclesIds($id_order, $sort = true)
    {
        $order = Order::findOne($id_order);

        if (!$order) return false;

        if ($order->status != Order::STATUS_NEW && $order->status != Order::STATUS_IN_PROCCESSING) return false;

        $order->suitable_vehicles = null;
        $order->save(false);

        $vehicles = ($sort)
            ? $order->getSortSuitableVehicles(false)
            : $order->getSuitableVehicles(false);
        $res = [];
//        $return = '';
        foreach ($vehicles as $vehicle) {
            if($vehicle->user->email) {
                $res[] = $vehicle->id;
            }
//            echo $vehicle->id . "\n";
        }
        $order->suitable_vehicles = $res;
        $order->save(false);


        if($order->auto_find && $order->suitable_vehicles){
            functions::startCommand('console/auto-find',
                [$order->id]);
        }
    }

}