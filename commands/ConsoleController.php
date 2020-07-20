<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 14.07.2020
 * Time: 15:54
 */

namespace app\commands;


use app\models\Message;
use yii\console\Controller;

class ConsoleController extends Controller
{
    public function actionAutoFind(){
        $mes = new Message([
            'id_to_user' => 1,
            'title' => 'Проверка'
        ]);
        $mes->sendPush(false);
        sleep(10);
        $mes->sendPush(false);
        sleep(10);
        $mes->sendPush(false);
    }

}