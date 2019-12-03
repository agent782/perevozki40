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
        if($push){
            $mes = new Message([
               'id_to_user' => $idAdmin,
                'id_order' => $id_order,
                'title' => 'Заказ №' . $id_order . ' - ' . $order->statusText,
                'text' => '',
                'url' => Url::to('/logist/order', true)
            ]);
            $mes->sendPush(false);
        }

        if($email){
            functions::sendEmail(
                $emailAdmin,
                null,
                'Заказ №' . $id_order . ' - ' . $order->statusText,
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
}