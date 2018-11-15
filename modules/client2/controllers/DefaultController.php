<?php

namespace app\modules\client\controllers;

use app\models\BodyType;
use app\models\LoadingType;
use app\models\Route;
use app\models\Vehicle;
use app\models\VehicleType;
use yii\web\Controller;
use app\models\Order;
use yii\helpers\ArrayHelper;

/**
 * Default controller for the `client` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionOrder()
    {
        $order = new Order();
        $route = new Route();
        //$orders = Order::find()->all();
        return $this->render('order', [
            'order' => $order,
            'route' => $route,
            //'orders' => $orders,
        ]);
    }

    public function actionAjax()
    {
        if (\Yii::$app->request->isAjax) {
            if (\Yii::$app->request->post('vehicle_type')) {
                // $res = BodyTruck::find()->asArray()->where(['id_type_vehicle' => \Yii::$app->request->post('vehicle_type')])->all();
                $veh_type = \Yii::$app->request->post('vehicle_type');
                $vehicletypes = VehicleType::find()
                    ->where(['id' => $veh_type])
                    ->all();
                //$vehicles = $vehicletypes[0] -> vehicle;
                $typebodies = array();
                foreach ($vehicletypes as $vtype) {
                    $vehicles = $vtype->vehicle;
                    //array_push($typebodies, $vehicles);
                    foreach ($vehicles as $veh) {
                        $bodytypes = $veh->bodytype;
                        foreach ($bodytypes as $btype) {
                            array_push($typebodies, $btype->attributes);

                        }
                    }
                }
                // var_dump(ArrayHelper::map($typebodies, 'id', 'body'));
                echo json_encode(ArrayHelper::map($typebodies, 'id', 'body'));

            }
        }
    }

    public function actionAjax2()
    {
        if (\Yii::$app->request->isAjax) {
            if (\Yii::$app->request->post('idtypebodies')){
                $idbodytypies = \Yii::$app->request->post('idtypebodies');
                $ltypies = array();
//                $ltype0 = LoadingType::find()->one();
//                array_push($ltypies, $ltype0->attributes);
                foreach ($idbodytypies as $idbodytype) {
                    $bodytypise = BodyType::find()->where(['id' => $idbodytype])->all();
                    foreach ($bodytypise as $bodytype) {
                        $vehicles = $bodytype->vehicle;
                        //array_push($typebodies, $vehicles);
                        foreach ($vehicles as $veh) {
                            $loadtypies = $veh->getLoadingtype()->all();
                            foreach ($loadtypies as $ltype) {
                                array_push($ltypies, $ltype->attributes);

                            }
                        }
                    }
                }
                echo json_encode(ArrayHelper::map($ltypies, 'id', 'type'));
            }else echo null;
        }else echo 3;

    }
}


//                $typebodies = array();
//                foreach ($vehicletypes as $vtype){
//                    $vehicles = $vtype->vehicle;
//                    foreach ($vehicles as $veh){
//                        $bodytypes = $veh -> bodytype;
//                        foreach ($bodytypes as $btype){
//                            array_push($typebodies, $btype);
//                            $typebodies = $btype;
//                        }
//                    }
//                }