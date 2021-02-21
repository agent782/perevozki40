<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 23.05.2019
 * Time: 9:58
 */

namespace app\components\functions;

use app\models\Invoice;
use app\models\Message;
use app\models\Order;
use app\models\Profile;
use app\models\setting\SettingFinance;
use app\models\User;
use Yii;
use app\components\functions\functions;
use yii\helpers\Url;

class emails
{
    static public function sendAfterUserRegistration($id_user){
        $profile = Profile::findOne(['id_user' => $id_user]);
        if($profile){
            $name = $profile->name . ' ' . $profile->patrinimic;
        }
        functions::sendEmail(
            $profile->email,
//            'agent782@ya.ru',
            Yii::$app->params['robotEmail'],
            'perevozki40.ru. Регистрация пользователя.',
            ['name' => $name],
            [
                'html' => 'views/user/after_registration_html.php',
                'text' => 'views/user/after_registration_text.php',
            ]

        );
    }

    static public function sendAfterClientRegistration($id_user){
        $profile = Profile::findOne(['id_user' => $id_user]);
        if($profile){
            $name = $profile->name . ' ' . $profile->patrinimic;
        }
        functions::sendEmail(
            $profile->email,
//            'agent782@ya.ru',
            Yii::$app->params['robotEmail'],
            'perevozki40.ru. Регистрация Клиента завершена.',
            ['name' => $name],
            [
                'html' => 'views/client/after_registration_html.php',
                'text' => 'views/client/after_registration_text.php',
            ]

        );
    }

    static public function sendAfterCarOwnerRegistration($id_user){
        $profile = Profile::findOne(['id_user' => $id_user]);
        if($profile){
            $name = $profile->name . ' ' . $profile->patrinimic;
        }
        functions::sendEmail(
            $profile->email,
//            'agent782@ya.ru',
            Yii::$app->params['robotEmail'],
            'perevozki40.ru. Регистрация Водителя завершена.',
            ['name' => $name],
            [
                'html' => 'views/car-owner/after_registration_html.php',
                'text' => 'views/car-owner/after_registration_text.php',
            ]

        );
    }

    static public function sendAfterUploadInvoice($id_user, $modelInvoice, $id_order, array $files = []){
        $profile = Profile::findOne(['id_user' => $id_user]);
        if($profile){
            $name = $profile->name . ' ' . $profile->patrinimic;
        }
        switch ($modelInvoice->type){
            case Invoice::TYPE_INVOICE:
                $sub = 'Заказ №' . $id_order . '. Счет выставлен.';
                break;
            case Invoice::TYPE_CERTIFICATE:
                $sub = 'Заказ №' . $id_order . '. Акт выполненных работ оформлен.';
                break;
        }
        $sendToEmails = [];
        $SettingFinance = SettingFinance::find()->one();
        if($SettingFinance->invoices_to_client_email) {
            if ($profile->email) $sendToEmails[] = $profile->email;
            if ($profile->email2) $sendToEmails[] = $profile->email2;
        }
        $company = $modelInvoice->company;
        if($company){
            if($SettingFinance->invoices_to_company_email){
                if($company->email) $sendToEmails[] = $company->email;
                if($company->email2) $sendToEmails[] = $company->email2;
                if($company->email3) $sendToEmails[] = $company->email3;
            }
        }

        if(!$sendToEmails) return false;

        $Order = Order::findOne($id_order);
        if($Order){
            if($Order->company){
                if($Order->company->email) $sendToEmails[] = $Order->company->email;
                if($Order->company->email2) $sendToEmails[] = $Order->company->email2;
                if($Order->company->email3) $sendToEmails[] = $Order->company->email3;
            }
        }
        functions::sendEmail(
            $sendToEmails,
//            'agent782@ya.ru',
            Yii::$app->params['financeEmail'],
            $sub,
            [
                'name' => $name,
                'type' => $modelInvoice->type

            ],
            [
                'html' => 'views/order/upload_invoice_html.php',
                'text' => 'views/order/upload_invoice_text.php',
            ],
            null,
            $files
        );
    }

    static public function sendLinkResetPassword($user){
        return functions::sendEmail(
            $user->email,
            Yii::$app->params['robotEmail'],
            'perevozki40.ru. Изменение пароля на сайте.',
            ['user' => $user],
            [
                'html' => 'views/passwordResetToken-html',
                'text' => 'views/passwordResetToken-text'
            ]

        );
    }

    static public function sendToAdminChangeOrder ($id_order, $idAdmin = 1
        , $emailAdmin = null
        , $push = true, $email = true){

        if(!$emailAdmin) $emailAdmin = Yii::$app->params['logistEmail']['email'];
        $order = Order::findOne($id_order);
        if(!$order) return false;
        $id_car_owner = '';
        if($carOwner = $order->carOwner) {
            $id_car_owner = ($carOwner->old_id)
                ? '"' . $carOwner->old_id . '"'
                : '#' . $carOwner->id_user;
        }
        if($push){
            $mes = new Message([
               'id_to_user' => $idAdmin,
                'id_order' => $id_order,
//                'title' => 'Заказ №' . $id_order . ' - (' . $id_car_owner . ') ' . $order->statusText,
                'title' => $id_car_owner . ' №' . $id_order . ' ' .$order->statusText,
                'text' => '',
                'url' => Url::to('/logist/order', true)
            ]);
            $mes->sendPush(false);
        }

        if($email){
            functions::sendEmail(
                $emailAdmin,
                null,
                'Заказ №' . $id_order . ' - ' . $order->statusText . ' (' . $id_car_owner . ')',
                []
            );
        }
    }

    static public function sendToAdminAfterSaveUser($id_user, $idAdmin = 1
        , $emailAdmin = null
        , $push = true, $email = true){

        if(!$emailAdmin) $emailAdmin = Yii::$app->params['adminEmail']['email'];
        $user = User::findOne($id_user);
        $sub = $user->rolesString . ' №' . $user->id . ' сохранен';

        if($email){
            functions::sendEmail(
                $emailAdmin,
                null,
                $sub,
                []
            );
        }
        if($push){
            $mes = new Message([
                'id_to_user' => $idAdmin,
                'title' => $sub,
                'text' => '',
                'url' => Url::to('/admin/users', true)
            ]);
            $mes->sendPush(false);
        }
    }

    static function sendToCarOwnerAfterCheckVehicles ($id_user, $brand_and_number, $type_mes, $user_name){
//        if(!$id_user || !$type_mes || !$brand_and_number || !$user_name) return false;

        $title = '';
        $text = 'Добрый день, ' . $user_name . '!<br><br>';

        if($type_mes == Message::TYPE_CHANGE_STATUS_VEHICLE){
            $title = 'Ваше ТС удалено из поиска';
            $text .= 'Статус Вашего ТС ' . $brand_and_number . ' изменен на НЕ АКТИВНО.<br><br>';
            $text .= 'В течении продолжительного времени Вы не приняли ни одного заказа. 
                Если Вы заинтересованы в дальнейшем сотрудничестве по этому ТС, 
                Вы можете восстановить его в разделе "Транспорт"->"Удаленные". <br><br>
                По всем вопросам Вы можете обращаться по телефонам или электронной почте.';
        } else if ($type_mes == Message::TYPE_WARNING_STATUS_VEHICLE){
            $title = 'Ваше ТС скоро будет удаленог из поиска';
            $text .= 'В скором времени статус Вашего ТС ' . $brand_and_number . ' будет изменен на НЕ АКТИВНО.<br><br>';
            $text .= 'В течении продолжительного времени Вы не приняли ни одного заказа.<br><br>
                Возможно Вам не приходят push или email уведомления, проверьте правильность настроек в разделе "Профиль".<br>
                Вы можете проверить данные по ТС в разделе "Мой транспорт". Возможно данные ТС устарели или стоит проверить
                выбранные тарифы. В скором времени ТС автоматически станет НЕ АКТИВНЫМ. 
                По всем вопросам обращайтесь по телефонам или электронной почте.
            ';
        } else {
            return false;
        }

        $Message = new Message([
            'text' => $text,
            'title' => $title,
            'text_push' => '',
//            "id_to_user" => $id_user,
            "id_to_user" => '186',
            'type' => $type_mes,
            'url' => Url::to('/vehicle', true),
        ]);
        $user = User::findOne($id_user);
        if($user) {
            if($user->push_ids) {
                $Message->sendPush(true);
            } else {
                $Message->save();
            }
            if($user_email = $user->email) {
                functions::sendEmail(
                    $user_email,
                    null,
                    $title,
                    [
                        'message' => $text
                    ],
                    [
                        'html' => 'views/car-owner/after_check_vehicles_html',
                        'text' => 'views/car-owner/after_check_vehicles_text'
                    ]
                );
            }

            if($user_email2 = $user->profile->email2) {
                functions::sendEmail(
                    $user_email2,
                    null,
                    $title,
                    [
                        'message' => $text
                    ],
                    [
                        'html' => 'views/car-owner/after_check_vehicles_html',
                        'text' => 'views/car-owner/after_check_vehicles_text'
                    ]

                );
            }
        }


    }

}