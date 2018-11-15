<?php

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 17.01.2018
 * Time: 14:36
 */
namespace app\components;
use app\models\Profile;
use app\models\User;
use Yii;
use yii\db\ActiveRecord;
use yii\base\Behavior;


class UserBehaviors extends Behavior
{
    public $_id;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'addUserRole',
//            ActiveRecord::EVENT_AFTER_INSERT => 'saveProfile',
        ];
    }

    public function addUserRole($event){
        $auth = Yii::$app->authManager;
        $role = $auth->getRole('user');
        $auth->assign($role, $this->owner->id);
    }


}