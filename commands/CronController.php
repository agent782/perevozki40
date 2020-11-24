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
use app\models\Vehicle;
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
                $sms->sendAndSave();

                $mesAdmin = new Message([
                    'id_to_user' => 1,
                    'title' => 'ДР ' . ' #' . $profile->id_user
                ]);
                $mesAdmin->sendPush(false);
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
            $Message .= $count_mes . ' - предложено заказов,которые в итоге приняли и выполнили другие водители <br><br>';
            if($sum_not_confirmed){
                $Message .= $sum_not_confirmed . 'р. - общая сумма предложенных заказов, которые в итоге приняли и выполнили другие водители<br><br>';
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
            $User = User::findOne($id);
            functions::sendEmail(
                [$User->email, $User->profile->email2],
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

    public function actionCheckVehicles(){
        $activeTruckVehicles = Vehicle::find()
            ->where(['id_vehicle_type' => Vehicle::TYPE_TRUCK])
            ->andWhere(['in', 'status', [Vehicle::STATUS_ACTIVE, Vehicle::STATUS_ONCHECKING]])
            ->all();

//        $activeVehicles = Vehicle::find()
//            ->where(['id_user' => 1])
//            ->all();

        $stat_warning = [];
        $stat_change = [];

        foreach ($activeTruckVehicles as $vehicle){

            $user = $vehicle->user;
//            echo $vehicle->update_at ? $vehicle->update_at : $vehicle->create_at;
//            echo "\n\n";
            $sendEmailChangeStatus = false;
            $sendEmailWarningAboutChangeStatus = false;

            $order = $vehicle->getOrders()
                ->andWhere(['!=', 'status', Order::STATUS_CANCELED])
                ->orderBy(['datetime_start' => SORT_DESC])
                ->one();
            $days_elapsed_update_vehicle = round((time()
                    - strtotime(($vehicle->update_at) ? $vehicle->update_at : $vehicle->create_at))/(60*60*24));

            $days_elapsed_last_order = 0;

            if(!$order) {
                if ($vehicle->tonnage <= 1.5) {
                    if($days_elapsed_update_vehicle > 30){
                        if($days_elapsed_update_vehicle > 45){
                            // Изменить статус на Не активно
                            // отправить письмо
                            $sendEmailChangeStatus = true;
                        } else {
                            // Раз в %n дня отправлять письмо о скором изменении статуса
                            if(!($days_elapsed_update_vehicle%4)){
                                $sendEmailWarningAboutChangeStatus = true;
                            }
                        }
                    }
                } else if ($vehicle->tonnage > 1.5 && $vehicle->tonnage < 4) {
                    if($days_elapsed_update_vehicle > 45){
                        if($days_elapsed_update_vehicle > 55){
                            $sendEmailChangeStatus = true;
                        } else {
                            if(!($days_elapsed_update_vehicle%3)){
                                $sendEmailWarningAboutChangeStatus = true;
                            }
                        }
                    }
                } else if ($vehicle->tonnage >= 4 && $vehicle->tonnage <= 10) {
                    if($days_elapsed_update_vehicle > 60){
                        if($days_elapsed_update_vehicle > 70){
                            $sendEmailChangeStatus = true;
                        } else {
                            if(!($days_elapsed_update_vehicle%3)){
                                $sendEmailWarningAboutChangeStatus = true;
                            }
                        }
                    }
                } else {
                    if($days_elapsed_update_vehicle > 90){
                        if($days_elapsed_update_vehicle > 120){
                            $sendEmailChangeStatus = true;
                        } else {
                            if(!($days_elapsed_update_vehicle%5)){
                                $sendEmailWarningAboutChangeStatus = true;
                            }
                        }
                    }
                }

            } else {
                $days_elapsed_last_order =round( (time() - strtotime($order->datetime_start))/(60*60*24));

                if ($vehicle->tonnage <= 1.5) {
                    if($days_elapsed_last_order > 30 ){
                        if($days_elapsed_last_order > 40){
                            $sendEmailChangeStatus = true;
                        } else {
                            if(!($days_elapsed_last_order%3)){
                                $sendEmailWarningAboutChangeStatus = true;
                            }
                        }
                    }
                } else if ($vehicle->tonnage > 1.5 && $vehicle->tonnage < 4) {
                    if($days_elapsed_last_order > 45 ){
                        if($days_elapsed_last_order > 55){
                            $sendEmailChangeStatus = true;
                        } else {
                            if(!($days_elapsed_last_order%4)){
                                $sendEmailWarningAboutChangeStatus = true;
                            }
                        }
                    }
                } else if ($vehicle->tonnage >= 4 && $vehicle->tonnage <= 10) {
                    if($days_elapsed_last_order > 60 ){
                        if($days_elapsed_last_order > 75){
                            $sendEmailChangeStatus = true;
                        } else {
                            if(!($days_elapsed_last_order%5)){
                                $sendEmailWarningAboutChangeStatus = true;
                            }
                        }
                    }
                } else {
                    if($days_elapsed_last_order > 90 ){
                        if($days_elapsed_last_order > 120){
                            $sendEmailChangeStatus = true;
                        } else {
                            if(!($days_elapsed_last_order%7)){
                                $sendEmailWarningAboutChangeStatus = true;
                            }
                        }
                    }
                }
            }
            $days = round((time() - strtotime('01.11.2020'))/(60*60*24));

            if ($vehicle->tonnage <= 1.5) {
                if($sendEmailChangeStatus){
                    if($days < 10){
                        $sendEmailChangeStatus = false;
                        if(!($days%3)) {
                            $sendEmailWarningAboutChangeStatus = true;
                        }
                    }
                }
            } else if ($vehicle->tonnage > 1.5 && $vehicle->tonnage < 4) {
                if($sendEmailChangeStatus){
                    if($days < 15){
                        $sendEmailChangeStatus = false;
                        if(!($days%5)) {
                            $sendEmailWarningAboutChangeStatus = true;
                        }
                    }
                }
            } else if ($vehicle->tonnage >= 4 && $vehicle->tonnage <= 10) {
                if($sendEmailChangeStatus){
                    if($days < 20){
                        $sendEmailChangeStatus = false;
                        if(!($days%6)) {
                            $sendEmailWarningAboutChangeStatus = true;
                        }
                    }
                }
            } else {
                if($sendEmailChangeStatus){
                    if($days < 30){
                        $sendEmailChangeStatus = false;
                        if(!($days%7)) {
                            $sendEmailWarningAboutChangeStatus = true;
                        }
                    }
                }
            }

            if($sendEmailWarningAboutChangeStatus){
                echo '1 ' . $days_elapsed_last_order . ' ' . $days_elapsed_update_vehicle
                    .  '  ' . $user->profile->fioShort . ' '
                    . $vehicle->brandAndNumber
                    . "\n";
                emails::sendToCarOwnerAfterCheckVehicles($user->id,
                    $vehicle->brandAndNumber, Message::TYPE_WARNING_STATUS_VEHICLE, $user->profile->name);
                $stat_warning [] = $days_elapsed_last_order . ' ' . $days_elapsed_update_vehicle
                    .  '  ' . $user->profile->fioShort . ' '
                    . $vehicle->brandAndNumber . ' #' . $vehicle->id . ' ПРДУПРЕЖДЕНИЕ';
            }

            if($sendEmailChangeStatus){
                echo '2 ' . $days_elapsed_last_order . ' ' . $days_elapsed_update_vehicle
                    . '  ' . $user->profile->fioShort . ' '
                    . $vehicle->brandAndNumber
                    . "\n";
                emails::sendToCarOwnerAfterCheckVehicles($user->id,
                    $vehicle->brandAndNumber, Message::TYPE_CHANGE_STATUS_VEHICLE, $user->profile->name);
                $vehicle->status = Vehicle::STATUS_NOT_ACTIVE;
                $vehicle->updateAttributes(['status']);
                $stat_change [] = $days_elapsed_last_order . ' ' . $days_elapsed_update_vehicle
                    .  '  ' . $user->profile->fioShort . ' '
                    . $vehicle->brandAndNumber . ' #' . $vehicle->id . ' НЕ АКТИВНО';
            }
        }
        $mes_to_admin = '';
        foreach ($stat_change as $item){
            $mes_to_admin .= $item . "<br>";
        }
        $mes_to_admin .= "<br><br>";
        foreach ($stat_warning as $item){
            $mes_to_admin .= $item . '<br>';
        }

        if($mes_to_admin){
            functions::sendEmail(
                Yii::$app->params['adminEmail']['email'],
                null,
                'Изменение статусов ТС',
                ['message' => $mes_to_admin],
                [
                    'html' => 'views/car-owner/after_check_vehicles_html',
                    'text' => 'views/car-owner/after_check_vehicles_text'
                ]

            );
        }
    }
}