<?php

namespace app\modules\admin\controllers;

use app\components\functions\functions;
use app\models\Order;
use app\models\OrderSearch;
use app\models\VehicleSearch;
use MongoDB\BSON\Timestamp;
use yii\base\DynamicModel;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use app\models\User;
use app\models\Payment;
use app\models\Vehicle;

class TestController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin']
                    ]
                ]
            ]
        ];
    }

    public function actionIndex()
    {
        echo $date = '13.02.2020 12:00';
        $day = (24*3600);
        $reminder = strtotime($date) % $day;
        echo '<br>';
        echo $reminder;
        echo '<br>';
        if($reminder > 43140 && $reminder <= 75540){
            echo date('d.m.Y H:i', round (strtotime($date)/($day)) * ($day) - $day);
        } else {
            echo date('d.m.Y H:i', round (strtotime($day)/($day)) * ($day) );
        }

        return;
        return $this->render('index');
    }

    public function actionQueueCarOwners($id_order = null){
        $order = null;
        $model = new DynamicModel(['id_order']);
        $model->addRule(
            'id_order', 'number'
        );
        $model->id_order = $id_order;
        $dataProvider = new ArrayDataProvider([
            'pagination' => ['pageSize' => 100]
        ]);

//        $dataProvider = new ArrayDataProvider();
        if($model->load(\Yii::$app->request->post())){
            $order = Order::findOne($model->id_order);
            if($order){
                set_time_limit(200);
                $dataProvider->allModels = $order->getSortSuitableVehicles();
//                return var_dump($dataProvider->allModels);
            } else {
                functions::setFlashWarning('Заказ не найден!');
            }
        }
        return $this->render('queue-car-owners', [
            'dataProvider' => $dataProvider,
            'model' => $model,
            'order' => $order
        ]);
    }



}
