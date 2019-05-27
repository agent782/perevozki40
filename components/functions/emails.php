<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 23.05.2019
 * Time: 9:58
 */

namespace app\components\functions;

use app\models\Profile;
use Yii;
use app\components\functions\functions;

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
}