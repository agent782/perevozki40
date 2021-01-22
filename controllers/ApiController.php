<?php

namespace app\controllers;

use Yii;
use app\models\Profile;
use app\models\User;
use yii\helpers\Json;
use yii\rest\ActiveController;
use yii\web\Response;

class ApiController extends ActiveController
{
    public $modelClass = User::class;


    public function actionViewer(){
        return (Profile::findOne(['id_user' => '1']));
    }

    public function actionLogin(){
        $request = Yii::$app->request;
        $username = $request->post('phone');
        $password = $request->post('password');

//        $username = '1111111111';
//        $password = '123456';

        $return = [
            'userid' => null,
            'name' => null
        ];

        Yii::$app->response->format = Response::FORMAT_JSON;

        if($username){
            $User = User::findOne(['username' => $username]);
            if($User){
                if($User->validatePassword($password)){
                    $return['userid'] = $User->id;
                    $return['name'] = $User->profile->name;
                    return $return;
                }
            }
        }

        return $return;

    }



}
