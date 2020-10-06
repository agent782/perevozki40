<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 06.12.2018
 * Time: 9:05
 */

namespace app\commands;

use app\components\functions\emails;
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
use yii\helpers\ArrayHelper;

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
                $text = $profile->name
                    . '! С днем рожденья поздравляем,
                    Счастья, прибыли желаем.
                    Дом прекрасный и уют,
                    Где родные всегда ждут.
                    perevozki40.ru';

                $sms = new Sms($profile->phone, $text);
//                $sms->sendAndSave();

                $mesAdmin = new Message([
                    'id_to_user' => 1,
                    'title' => 'ДР ' . ' #' . $profile->id_user
                ]);
//                $mesAdmin->sendPush(false);
            }

        } else echo 'no';
    }

    public function actionCarOwnerReport($days = 7, $startDate = null){
        date_default_timezone_set('Europe/Moscow');
        if(!$startDate) $startDate = date('d.m.Y', (time() - 60*60*24*7));
        echo $date = $startDate . "\n";
        $dates[$days] = $startDate;
        for ($i = $days-1; $i>0; $i--){
            $dates[$i] = date('d.m.Y' , (strtotime($date) - 60*60*24));
            $date = $dates[$i];
            echo $dates[$i] . "\n";
        }

        $Orders = Order::find()
            ->where(['in', 'status', [
                Order::STATUS_CONFIRMED_VEHICLE,
                Order::STATUS_CONFIRMED_CLIENT,
                Order::STATUS_CONFIRMED_SET_SUM]])
            ->andWhere(['>=', 'datetime_start', strtotime($dates[1])])
            ->andWhere(['<', 'datetime_start', (strtotime($dates[$days])+ 3600*24)])
            ->orderBy('id_car_owner')
            ->all();
        $Users = [];
        foreach ($Orders as $order){
            if(!array_key_exists($order->id_car_owner, $Users)){
                $Users[$order->id_car_owner] = [
                    'messages' => [],
                    'orders' => [],
                    'sum_finish' => 0,
                    'sum_re_finish' => 0,
                    'sum_not_confirmed' => 0,
                    'name' => User::findOne($order->id_car_owner)->profile->name
                ];
            }
            $Users[$order->id_car_owner]['orders'][] = $order->id;
            $Users[$order->id_car_owner]['sum_finish'] += $order->cost_finish_vehicle;
            if($order->re) {
                $Users[$order->id_car_owner]['sum_re_finish'] += $order->cost_finish_vehicle;
            }
        }


//        print_r($Users);

        $Messages = Message::find()
            ->where(['type' => Message::TYPE_ALERT_CAR_OWNER_NEW_ORDER])
            ->andWhere(['>=', 'create_at', strtotime($dates[1])])
            ->andWhere(['<=', 'create_at', (strtotime($dates[$days])+ 3600*24)])
            ->orderBy('id_to_user')
            ->select(['id_to_user', 'id_order'])
            ->all()
        ;
        foreach ($Messages as $message){
            if($order = $message->order) {
                if($order->status == Order::STATUS_CONFIRMED_VEHICLE
                    || $order->status == Order::STATUS_CONFIRMED_CLIENT
                    || $order->status == Order::STATUS_CONFIRMED_SET_SUM
                ) {
                    if (!array_key_exists($message->id_to_user, $Users)) {
                        $Users[$message->id_to_user] = [
                            'messages' => [],
                            'orders' => [],
                            'sum_finish' => 0,
                            'sum_re_finish' => 0,
                            'sum_not_confirmed' => 0,
                            'name' => User::findOne($message->id_to_user)->profile->name
                        ];
                    }

                    $Users[$message->id_to_user]['messages'][] = $message->id_order;
                    if ($order->id_car_owner != $message->id_to_user) {
                        $Users[$message->id_to_user]['sum_not_confirmed'] += $order->cost_finish_vehicle;
                    }

                }
            }

        }
//        print_r($Users);
        foreach ($Users as $id => $user){
            $Title = 'Статистика за прошлую неделю.';
            $Users[$order->id_car_owner] = [
                'messages' => [],
                'orders' => [],
                'sum_finish' => 0,
                'sum_re_finish' => 0,
                'sum_not_confirmed' => 0
            ];
            $count_mes = count($user['messages']);
            $count_orders = count($user['orders']);
            $sum_finish = $user['sum_finish'];
            $sum_re_finish = $user['sum_re_finish'];
            $sum_not_confirmed = $user['sum_not_confirmed'];

            $Message = 'Добрый день, ' . $user['name'] . '!<br><br>';
            $Message .= 'Ваша статистика с '
                . $dates[1] . ' по ' . $dates[$days] . '<br><br>';
            $Message .= $count_orders . ' - заказов выполнено <br> <br>';
            if($count_orders) {
                $Message .= $sum_finish . 'р. - общая сумма выполненных заказов <br><br>';
            }
            $Message .= $count_mes . ' - предложено заказов. <br><br>';
            if($sum_not_confirmed){
                $Message .= $sum_not_confirmed . 'р. - общая сумма непринятых заказов<br><br>';
            }
            if($count_mes){
                $Message .= (round($count_orders/$count_mes, 2)*100)
                    . '% - процент принятых заказов<br><br>';
            }
            $Message .= 'Для оперативного уведомления о новых заказах подключайте PUSH-уведомления на Ваш смартфон.
                Смотрите видеоинструкцию по подключению https://youtu.be/JlzyTWkNM8g <br><br>';
            $Message .= 'Если Вам не приходят электронные письма на указанный в Вашем профиле 
                e-mail адрес, проверьте его актуальность. <br><br>';
            $Message .= 'Чем ниже процент принятых заказов, тем ниже Ваш рейтинг и тем реже Вы получаете 
                уведомления о новых заказах. Что бы этот процент не падал, регулярно проставляйте в "Календаре занятости" 
                актуальные статусы Ваших ТС (занят, свободен или частично занят), Это исключит отправку уведомлений, 
                когда Ваше ТС занято.<br><br>';

            $Mes = new Message([
                'id_to_user' => $id,
                'type' => Message::TYPE_STATISTIC_CAR_OWNER,
                'title' => $Title,
                'text' => $Message,
                'text_push' => '',
                'url' => Url::to(['/message'], true),
                'push_status' => Message::STATUS_NEED_TO_SEND,
                'email_status' => Message::STATUS_NEED_TO_SEND,
            ]);

            $Mes->sendPush(true);

            functions::sendEmail(
                [$user->email, $user->profile->email2],
                null,
                $Title,
                ['message' => $Message],
                [
                    'html' => 'views/car-owner/week_report_html',
                    'text' => 'views/car-owner/week_report_text'
                ]
            );
            sleep(10);
        }
        functions::sendEmail(
             [Yii::$app->params['adminEmail']['email']],
            null,
            'Отправлено ' . count($Users) . ' отчетов за период с '
            . $dates[1] . ' по ' . $dates[$days],
            []
        );
    }
}