<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 06.12.2018
 * Time: 9:05
 */

namespace app\commands;

use app\models\User;
use Yii;
use app\components\functions\functions;
use app\models\Order;
use app\models\Setting;
use yii\console\Controller;
use yii2tech\crontab\CronJob;
use yii2tech\crontab\CronTab;

class CronController extends Controller
{
    public function actionMonitoringExpiredOrders(){
        $setting = Setting::find()->one();
        if(!$setting->FLAG_EXPIRED_ORDER) {

            $orders = Order::find()
                ->where(['status' => Order::STATUS_EXPIRED])
                ->andWhere(['FLAG_SEND_EMAIL_STATUS_EXPIRED'=>0])
                ->all();

            foreach ($orders as $order) {
                $user = User::find()->where(['id' => $order->id_user])->one();
                functions::sendEmail(
                    $user->email,
                    null,
                    'Заказ №' . $order->id . '. Машина не найдена.',
                    [
                        'order' => $order,
                        'user' => $user
                    ],
                    [
                        'html' => 'views/Order/expiredOrder_html',
                        'text' => 'views/Order/expiredOrder_text'
                    ]
                );
                $order->FLAG_SEND_EMAIL_STATUS_EXPIRED = 1;
                $order->scenario = $order::SCENARIO_UPDATE_STATUS;
                $order->save();
//                $order->update();
//                echo $order->id . "\r\n";
            }
            $setting->FLAG_EXPIRED_ORDER = 1;
//            $setting->save();
            if($setting->save()) echo 'y'; else echo 'n';
        }
    }


}