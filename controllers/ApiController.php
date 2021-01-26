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
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request->headers;
        $username = $request['phone'];
        $password = $request['password'];

        $return = [
            'userid' => null,
            'name' => null,
            'token' => null
        ];


        if($username){
            $User = User::findOne(['username' => $username]);
            if($User){
                if($User->validatePassword($password)){
                    $return['userid'] = $User->id;
                    $return['name'] = $User->profile->name;
                    $return['token'] = $User->auth_key;
                    return $return;
                }
            }
        }

        return $return;

    }



}
