<?php

namespace app\controllers;

use app\components\ApiComponent;
use app\models\Order;
use Yii;
use app\models\Profile;
use app\models\User;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\Json;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use yii\helpers\Url;
use app\models\PriceZone;

class ApiController extends ApiComponent
{
    public $modelClass = User::class;

public function beforeAction($action)
{
    if($action->id == 'login'){
        $this->detachBehavior('authenticator');
    }
    return parent::beforeAction($action); // TODO: Change the autogenerated stub
}

    public function actionViewer(){
        return (Profile::findOne(['id_user' => '1']));
    }

    public function actionLogin(){
//        Yii::$app->response->format = Response::FORMAT_JSON;

//        return json_decode(Yii::$app->request->rawBody)->phone;
        $request = json_decode(Yii::$app->request->rawBody);
        if(!$request){
            return [
                'status' => 'ERROR'
            ];
        }
//        return $request;
        if(!$request->phone || !$request->password){
            return [
                'status' => 'ERROR'
            ];
        }
        $username = $request->phone;
        $password = $request->password;

        if($username){
            $User = User::findOne(['username' => $username]);
            if($User){
                if($User->validatePassword($password)){
                    if($request->firebase_id){
                        if(is_array($User->firebase_ids)){
                            if(!in_array($request->firebase_id, $User->firebase_ids)) {
                                $firebase_ids = $User->firebase_ids;
                                $firebase_ids[] = $request->firebase_id;
                                $User->firebase_ids = $firebase_ids;
                                $User->scenario = $User::SCENARIO_SAVE;
                                $User->save();
                            }
                        } else {
				            $firebase_ids = [];
                            $firebase_ids [] = $request->firebase_id;
				            $User->firebase_ids = $firebase_ids;
                            $User->scenario = $User::SCENARIO_SAVE;
                            $User->save();
                        }
                    }
                    $return = [
                        'status' => 'OK'
                    ];

                    $return['userid'] = $User->id;
                    $return['roles'] = $User->getRoles(true);
                    $return['token'] = $User->auth_key;
                    return $return;
                }
            }
        }

        return [
            'status' => 'ERROR'
        ];
    }

    public function actionLogout(){
        $request = json_decode(Yii::$app->request->rawBody);
        $request->userid;
        if(!$request->userid
            || $request->userid != Yii::$app->user->id
        ){
            throw new UnauthorizedHttpException();
        }
        $User = Yii::$app->user->identity;
//	return $User->firebase_ids;

        if($request->firebase_id){
         //   if(is_array($User->firebase_ids) && in_array($request->firebase_id, $User->firebase_ids)){
		if(($key=array_search($request->firebase_id, $User->firebase_ids)) !== false){ 
			$firebase_ids = $User->firebase_ids;
            unset($firebase_ids[$key]);
			$User->firebase_ids = $firebase_ids;
			if(!$User->firebase_ids) $User->firebase_ids = null;
			$User->scenario = $User::SCENARIO_SAVE;
               		 $User->save();
		}

           // }
        }

        return ['status' => 'OK'];

    }

    public function actionOrders(){
        $request = json_decode(Yii::$app->request->rawBody);

        if(!$request->userid
            || $request->userid != Yii::$app->user->id
        ){
            throw new UnauthorizedHttpException();
        }
        $User = Yii::$app->user->identity;
//        $User = new User();

        $return = [
            'new' => [],
            'in_proccess' => [],
            'completed' => []
        ];
        $Orders_new = Order::find()
            ->where(['in', 'status', [Order::STATUS_NEW, Order::STATUS_IN_PROCCESSING]])
            ->all();

        if($User->canRole('user')
            || $User->canRole('client')
            || $User->canRole('vip_client')
        ){
            $Orders = Order::find()
                ->where(['id_user' => $User->id])
                ->orderBy(['datetime_start' => SORT_DESC])
//                ->andWhere(['in', 'status', [Order::STATUS_NEW, Order::STATUS_IN_PROCCESSING]])
                ->all();
            if($Orders){
                foreach ($Orders as $order){
                    $route = $order->route;
                    $real_route = $order->realRoute;
                    if($order->status == Order::STATUS_NEW
                        || $order->status == Order::STATUS_IN_PROCCESSING){
                        $return ['new'][] = [
                            'id' => $order->id,
                            'date' => $order->datetime_start,
                            'date_valid' => $order->valid_datetime,
                            'route' => $route->fullRoute,
                            'info' => $order->getShortInfoForClient(false),
                            'type_payment' => $order->getPaymentText(false),
                            'rate' => $order->getIdsPriceZones(),
                            'url' => Url::to('/order/client', true)
                        ];
                    }
                    if($order->status == (Order::STATUS_VEHICLE_ASSIGNED)){
                        $return ['in_proccess'][] = [
                            'id' => $order->id,
                            'date' => $order->datetime_start,
                            'route' => $route->fullRoute,
                            'info' => $order->getShortInfoForClient(false),
                            'vehicle' => $order->getFullInfoAboutVehicle(true,true,
                                true,false),
                            'client' => $order->getClientInfo(false, true),
                            'rate' => PriceZone::findOne($order
                                ->id_pricezone_for_vehicle)
                                ->getTextWithShowMessageButton($route->distance, false, $order->discount),
                            'type_payment' => $order->getPaymentText(false),
                            'url' => Url::to('/order/client', true)
                        ];
                    }
                    if($order->status == Order::STATUS_CONFIRMED_VEHICLE
                            || $order->status == Order::STATUS_CONFIRMED_SET_SUM
                            || $order->status == Order::STATUS_CONFIRMED_CLIENT
                        ){
                        $return ['completed'][] = [
                            'id' => $order->id,
                            'date' => $order->datetime_finish,
                            'paid' => $order->paidText,
                            'cost' => $order->cost_finish,
                            'type_payment' => $order->getPaymentText(false),
                            'route' => $real_route->fullRoute,
                            'info' => $order->getShortInfoForClient(false),
                            'vehicle' => $order->getFullInfoAboutVehicle(false,false,false,
                                false),
                            'client' => $order->getClientInfo(false, false),
                            'url' => Url::to('/order/vehicle', true)
                        ];
                    }

                }
            }
            return $return;
        }

        if($User->canRole('car_owner')
            || $User->canRole('vip_car_owner')
        ){
            $Orders = Order::find()
                ->where(['id_car_owner' => $User->id])
                ->orderBy(['datetime_start' => SORT_DESC])
//                ->andWhere(['in', 'status', [Order::STATUS_NEW, Order::STATUS_IN_PROCCESSING]])
                ->all();
            if($Orders_new){
                foreach ($Orders_new as $order) {
                    $route = $order->route;
                    if (($order->status == Order::STATUS_NEW
                            || $order->status == Order::STATUS_IN_PROCCESSING)
                        && !$order->hide
                    ) {
                        $return ['new'][] = [
                            'id' => $order->id,
                            'date' => $order->datetime_start,
                            'date_valid' => $order->valid_datetime,
                            'route' => $route->fullRoute,
                            'info' => $order->getShortInfoForClient(false),
                            'type_payment' => $order->getPaymentText(false),
                            'rate' => '',
                            'url' => Url::to([
                                '/order/accept-order',
                                'id_order' => $order->id,
                                'id_user' => $User->id,
                            ], true)
                        ];
                    }
                }
            }
            if($Orders){
                foreach ($Orders as $order){
                    $route = $order->route;
                    $real_route = $order->realRoute;

                    if($order->status == (Order::STATUS_VEHICLE_ASSIGNED)){
                        $return ['in_proccess'][] = [
                            'id' => $order->id,
                            'date' => $order->datetime_start,
                            'route' => $route->fullRoute,
                            'info' => $order->getShortInfoForClient(false),
                            'client' => $order->getClientInfo(false, true),
                            'vehicle' => $order->getFullInfoAboutVehicle(true,true,true,
                                false),
                            'rate' => PriceZone::findOne($order
                                ->id_pricezone_for_vehicle)
                                ->getWithDiscount($order->getVehicleProcentPrice())
                                ->getTextWithShowMessageButton($route->distance, false),
                            'type_payment' => $order->getPaymentText(false),
                            'url' => Url::to([
                                '/order/finish-by-vehicle',
                                'id_order' => $order->id,
                            ], true)
                        ];
                    }
                    if($order->status == Order::STATUS_CONFIRMED_VEHICLE
                        || $order->status == Order::STATUS_CONFIRMED_SET_SUM
                        || $order->status == Order::STATUS_CONFIRMED_CLIENT
                    ){
                        $return ['completed'][] = [
                            'id' => $order->id,
                            'date' => $order->datetime_finish,
                            'paid' => $order->paidText,
                            'cost' => $order->cost_finish_vehicle,
                            'type_payment' => $order->getPaymentText(false),
                            'route' => $real_route->fullRoute,
                            'info' => $order->getShortInfoForClient(false),
                            'client' => $order->getClientInfo(false, false),
                            'vehicle' => $order->getFullInfoAboutVehicle(false,false,false,true),
                            'url' => Url::to('/order/vehicle', true)
                        ];
                    }

                }
            }
            return $return;
        }

        if($User->canRole('admin')
            || $User->canRole('dispetcher')
        ){
            if($Orders_new){
                foreach ($Orders_new as $order) {
                    $route = $order->route;
                    if (($order->status == Order::STATUS_NEW
                            || $order->status == Order::STATUS_IN_PROCCESSING)
                        && !$order->hide
                    ) {
                        $return ['new'][] = [
                            'id' => $order->id,
                            'date' => $order->datetime_start,
                            'date_valid' => $order->valid_datetime,
                            'route' => $route->fullRoute,
                            'info' => $order->getShortInfoForClient(false),
                            'type_payment' => $order->getPaymentText(false),
                            'rate' => '',
                            'url' => Url::to('/logist/order', true)
                        ];
                    }
                }
            }

            $Orders = Order::find()
                ->orderBy(['datetime_start' => SORT_DESC])
                ->limit(100)
                ->all();
            if($Orders){
                foreach ($Orders as $order){
                    $route = $order->route;
                    $real_route = $order->realRoute;
                    if($order->status == Order::STATUS_NEW
                        || $order->status == Order::STATUS_IN_PROCCESSING){
                        $return ['new'][] = [
                            'id' => $order->id,
                            'date' => $order->datetime_start,
                            'date_valid' => $order->valid_datetime,
                            'route' => $route->fullRoute,
                            'info' => $order->getShortInfoForClient(false),
                            'type_payment' => $order->getPaymentText(false),
                            'rate' => $order->getIdsPriceZones(),
                            'url' => Url::to('/logist/order', true)
                        ];
                    }
                    if($order->status == (Order::STATUS_VEHICLE_ASSIGNED)){
                        $return ['in_proccess'][] = [
                            'id' => $order->id,
                            'date' => $order->datetime_start,
                            'route' => $route->fullRoute,
                            'info' => $order->getShortInfoForClient(false),
                            'client' => $order->getClientInfo(false, true),
                            'vehicle' => $order->getFullInfoAboutVehicle(true,true,true,
                                false),
                            'rate' => PriceZone::findOne($order
                                ->id_pricezone_for_vehicle)
                                ->getTextWithShowMessageButton($route->distance, false)
                                . '<br>'
                                .  PriceZone::findOne($order
                                    ->id_pricezone_for_vehicle)
                                    ->getWithDiscount($order->getVehicleProcentPrice())
                                    ->getTextWithShowMessageButton($route->distance, false)
                            ,
                            'type_payment' => $order->getPaymentText(false),
                            'url' => Url::to([
                                '/order/finish-by-vehicle',
                                'id_order' => $order->id,
                            ], true)
                        ];
                    }
                    if($order->status == Order::STATUS_CONFIRMED_VEHICLE
                        || $order->status == Order::STATUS_CONFIRMED_SET_SUM
                        || $order->status == Order::STATUS_CONFIRMED_CLIENT
                    ){
                        $return ['completed'][] = [
                            'id' => $order->id,
                            'date' => $order->datetime_finish,
                            'paid' => $order->paidText,
                            'cost' => $order->cost_finish_vehicle,
                            'type_payment' => $order->getPaymentText(false),
                            'route' => $real_route->fullRoute,
                            'info' => $order->getShortInfoForClient(false),
                            'client' => $order->getClientInfo(false, false),
                            'vehicle' => $order->getFullInfoAboutVehicle(false,false,false,true),
                            'url' => Url::to('/logist/order', true)
                        ];
                    }

                }
            }
            return $return;
        }
    }



}
