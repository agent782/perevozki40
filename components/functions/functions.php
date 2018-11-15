<?php
    namespace app\components\functions;
    use app\models\User;
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 16.01.2018
 * Time: 12:45
 */
class functions
{
    public function findUser($id){
        return User::findOne($id);
    }

    public function findCurrentUser(){
        return User::findOne(\Yii::$app->user->identity->getId());
    }

    public function findAllUsers(){
        return User::find()->all();
    }
}