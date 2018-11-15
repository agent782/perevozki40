<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 10.08.2017
 * Time: 11:15
 */

namespace app\controllers;
use yii\db\ActiveRecord;
use Yii;
use yii\web\Controller;

class RbacController extends Controller
{
    public function actionAddRole($role = 'test'){
        $auth = Yii::$app->authManager;
        if($auth->getRole($role)) {
            echo 'USER EXISTS';
            return NULL;
        }
        $_role = $auth->createRole($role);
        echo $_role->name . ' CREATED';
        return $auth->add($_role);


    }
}