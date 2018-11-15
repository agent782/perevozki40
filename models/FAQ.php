<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 01.04.2018
 * Time: 10:27
 */

namespace app\models;


use app\components\widgets\ShowMessageWidget;
use yii\base\Model;

class FAQ extends Model
{
     const FAQ_MAX_COMPANIES = 1;
    const FAQ_ROLES_USERS = 2;

     public static function getFAQ ($id_user = 0, $constFAQ){
         $Profile = Profile::findOne(['id_user' => $id_user]);
         switch ($constFAQ){
             case self::FAQ_MAX_COMPANIES:
                 return '<p class = "text-info">У Вас зарегистрировано максимальное количество юр.лиц. <br>'
                     . 'Тип Вашей учетной записи "'
                     . $Profile->getRolesToString() . '" <br>'
                     . 'Максимальное количество юр.лиц - ' . $Profile->getMaxCompanies() . '. '
                     . ShowMessageWidget::widget(['helpMessage' => self::getFAQ($id_user, self::FAQ_ROLES_USERS)])
                     . '</p>'
                     ;
             case self::FAQ_ROLES_USERS:
                 return '
                    <p class = "text-info">
                        В нашем сервисе существует несколько типов учетных записей.
                        USER -
                        CLIENT -
                        VIP-CLIENT -
                        VEHICLE -
                        VIP-VEHICLE -
                    </p>
                 ';
         }
     }
}

