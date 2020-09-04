<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 06.12.2018
 * Time: 9:05
 */

namespace app\commands;

use app\models\Profile;
use app\models\Sms;
use app\models\User;
use Yii;
use app\components\functions\functions;
use app\models\Order;
use app\models\setting\Setting;
use yii\console\Controller;
use yii2tech\crontab\CronJob;
use yii2tech\crontab\CronTab;
use app\models\Message;
use yii\helpers\Url;

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

                $order->changeStatus(Order::STATUS_EXPIRED, $order->id_user);
//                $user = User::find()->where(['id' => $order->id_user])->one();

//                functions::sendEmail(
//                    $user->email,
//                    null,
//                    'Заказ №' . $order->id . '. Машина не найдена.',
//                    [
//                        'order' => $order,
//                        'user' => $user
//                    ],
//                    [
//                        'html' => 'views/Order/expiredOrder_html',
//                        'text' => 'views/Order/expiredOrder_text'
//                    ]
//                );
//                $Message = new Message([
//                    'id_to_user' => $order->id_user,
//                    'title' => 'Заказ №' . $order->id . '. Машина не найдена.',
//                    'text' => 'Вы можете повторить поиск в Личном кабинете в разделе Заказы на вкладке Отмененные.',
//                    'url' => Url::to(['/order/view', 'id' => $order->id], true),
//                    'push_status' => Message::STATUS_NEED_TO_SEND,
//                    'email_status' => Message::STATUS_NEED_TO_SEND,
//                    'can_review_client' => false,
//                    'can_review_vehicle' => false,
//                    'id_order' => $order->id
//                ]);
//                $Message->sendPush();

//                $order->FLAG_SEND_EMAIL_STATUS_EXPIRED = 1;
//                $order->scenario = $order::SCENARIO_UPDATE_STATUS;
//                $order->save();
            }
            $setting->FLAG_EXPIRED_ORDER = 1;
            if($setting->save()) echo 'y'; else echo 'n';
        }
    }

    public function actionTest(){
        echo 1;
       $Message = new Message(
           [
                    'id_to_user' => 73,
                    'title' => 'Заказ №' .
                        108 .
                        '. Машина не найдена.',
                    'text' => 'Машина не найдена.',
                    'url' => Url::to(['/order/view', 'id' => 108], true),
                    'push_status' => Message::STATUS_NEED_TO_SEND,
                    'email_status' => Message::STATUS_NEED_TO_SEND,
                ]
       );
//        $Message->url = Url::to(['/order/view', 'id' => 108], true);

//                var_dump($Message);
                $Message->save();

                echo 1;
    }

    public function actionHappyBirthday(){
        $Profiles = Profile::find()->where(
            'FROM_UNIXTIME(bithday, "%d%m") = FROM_UNIXTIME(UNIX_TIMESTAMP(NOW()), "%d%m")'
        )->all();
        if($Profiles) {
            foreach ($Profiles as $profile) {
                echo $profile->id_user . ' ' . $profile->bithday . "\n";
                $text = $profile->name
                    . '! С днем рожденья поздравляем,
                    Счастья, прибыли желаем.
                    Дом прекрасный и уют,
                    Где родные всегда ждут.
                    perevozki40.ru';

                $sms = new Sms($profile->phone, $text);
                $sms->sendAndSave();

                $mesAdmin = new Message([
                    'id_to_user' => 1,
                    'title' => 'ДР ' . $profile->bithday . ' #' . $profile->id_user
                ]);
                $mesAdmin->sendPush(false);
            }

        } else echo 'no';
    }


}