<?php

namespace app\controllers;

use app\models\Profile;
use app\models\User;
use yii\helpers\Json;
use yii\rest\ActiveController;
use yii\web\Response;

class ApiController extends ActiveController
{
    public $modelClass = User::class;


    public function actionViewer(){
        return $this->asJson(Profile::findOne(['id_user' => '1']).bithday);
    }



}
