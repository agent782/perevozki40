<?php

namespace app\modules\admin\controllers;

use app\components\functions\functions;
use app\models\Order;
use yii\base\DynamicModel;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use app\models\User;
use app\models\Payment;
use app\models\Vehicle;
use app\models\TypePayment;

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
        return $this->render('index');
    }

    public function actionFindVehicles($id_order = null){
        $model = new DynamicModel(['id_order']);
        $model->addRule(
            'id_order', 'number'
        );
        $model->id_order = $id_order;
        $dataProvider = new ArrayDataProvider();
        if($model->load(\Yii::$app->request->post())){
            if($order = Order::findOne($model->id_order)){
                $vehicles = $order->getSuitableVehicles();
                if(!$vehicles) return false;
                $vehicle_car_owner = [];
                $vehicle_user_active = [];
                $res = [];
                foreach ($vehicles as $vehicle){
                    if($vehicle->user->canRole('car_owner')){
                        $vehicle_car_owner [] = $vehicle;
                    }
                }

                foreach ($vehicles as $vehicle){
                    $user = $vehicle->user;
                    if($user->canRole('user') && $user->status == User::STATUS_ACTIVE){
                        $vehicle_user_active [] = $vehicle;
                    }
                }

                usort($vehicle_car_owner, function (Vehicle $a, Vehicle $b){
                    if($this->type_payment == Payment::TYPE_BANK_TRANSFER) {
                        return $a->user->profile->balanceCarOwnerPayNow < $b->user->profile->balanceCarOwnerPayNow
                            ? -1 : 1;
                    } else {
                        return $a->user->profile->balanceCarOwnerPayNow < $b->user->profile->balanceCarOwnerPayNow
                            ? 1 : -1;
                    }
                });

                usort($vehicle_user_active, function (Vehicle $a, Vehicle $b){
                    if($this->type_payment == Payment::TYPE_BANK_TRANSFER) {
                        return $a->user->profile->balanceCarOwnerPayNow < $b->user->profile->balanceCarOwnerPayNow
                            ? -1 : 1;
                    } else {
                        return $a->user->profile->balanceCarOwnerPayNow < $b->user->profile->balanceCarOwnerPayNow
                            ? 1 : -1;
                    }
                });

                foreach ($vehicle_car_owner as $item){
                    $user = $item->user;
                    if(!in_array($user->id, $res)){
                        $res[] = $user->id;
                    }
                }

                foreach ($vehicle_user_active as $item){
                    $user = $item->user;
                    if(!in_array($user->id, $res)){
                        $res[] = $user->id;
                    }
                }
                $dataProvider->allModels = $res;
            } else {
                functions::setFlashWarning('Заказ не найден!');
            }
        }
        return $this->render('find-vehicles', [
            'dataProvider' => $dataProvider,
            'model' => $model
        ]);
    }

}
