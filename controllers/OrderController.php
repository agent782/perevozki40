<?php

namespace app\controllers;
use yii\helpers\Url;
use app\models\Message;
use app\models\User;
use yii\bootstrap\Html;
use yii\filters\AccessControl;
use app\components\functions\functions;
use app\models\Route;
use app\models\Service;
use app\models\TypePayment;
use app\models\Vehicle;
use Yii;
use app\models\Order;
use app\models\OrderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\BodyType;
use app\models\LoadingType;
use app\models\PriceZone;
use yii\helpers\ArrayHelper;
use streltcov\YandexUtils\GeoCoder;
use yii2tech\crontab\CronJob;
use yii2tech\crontab\CronTab;
/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['car_owner', 'client']
                    ]
                ],
            ]
        ];
    }

    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->searchForClient(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionClient(){
//        $searchModel = new OrderSearch();
//        $dataProviderNewOrders = $searchModel->searchForClientNEWOrders(Yii::$app->request->queryParams);
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider_newOrders = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider_in_process = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider_arhive = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider_expired_and_canceled = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider_newOrders->query
            ->where(['in', 'status', [Order::STATUS_NEW, 'status' => Order::STATUS_IN_PROCCESSING]])
            ->andWhere(['id_user' => Yii::$app->user->id]);
        $dataProvider_in_process->query
            ->where(['in','status', [Order::STATUS_VEHICLE_ASSIGNED, Order::STATUS_DISPUTE]])
            ->andWhere(['id_user' => Yii::$app->user->id]);
        $dataProvider_arhive->query
            ->where(['in', 'status' , [Order::STATUS_CONFIRMED_VEHICLE, Order::STATUS_CONFIRMED_CLIENT]])
            ->andWhere(['id_user' => Yii::$app->user->id]);
        $dataProvider_expired_and_canceled->query
            ->where(['in', 'status', [Order::STATUS_EXPIRED, Order::STATUS_CANCELED, Order::STATUS_NOT_ACCEPTED]])
            ->andWhere(['id_user' => Yii::$app->user->id]);

        return $this->render('client', [
            'searchModel' => $searchModel,
            'dataProvider_newOrders' => $dataProvider_newOrders,
            'dataProvider_in_process' => $dataProvider_in_process,
            'dataProvider_arhive' => $dataProvider_arhive,
            'dataProvider_expired_and_canceled' => $dataProvider_expired_and_canceled
        ]);
    }

    public function actionVehicle(){
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider_newOrders = $searchModel->searchCanVehicle(Yii::$app->request->queryParams);
        $dataProvider_in_process = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider_arhive = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider_expired_and_canceled = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider_in_process->query
            ->where(['in','status', [Order::STATUS_VEHICLE_ASSIGNED, Order::STATUS_DISPUTE]])
            ->andWhere(['in', 'id_vehicle', Yii::$app->user->identity->vehicleids])
        ;
        $dataProvider_arhive->query
            ->where(['in', 'status' , [Order::STATUS_CONFIRMED_VEHICLE, Order::STATUS_CONFIRMED_CLIENT]])
            ->andWhere(['in', 'id_vehicle', Yii::$app->user->identity->vehicleids])
        ;
        $dataProvider_expired_and_canceled->query
            ->where(['in', 'status', [Order::STATUS_EXPIRED, Order::STATUS_CANCELED, Order::STATUS_NOT_ACCEPTED]])
            ->andWhere(['in', 'id_vehicle', Yii::$app->user->identity->vehicleids]);

        return $this->render('vehicle', [
            'searchModel' => $searchModel,
            'dataProvider_newOrders' => $dataProvider_newOrders,
            'dataProvider_in_process' => $dataProvider_in_process,
            'dataProvider_arhive' => $dataProvider_arhive,
            'dataProvider_expired_and_canceled' => $dataProvider_expired_and_canceled
        ]);
    }

    /**
     * Displays a single Order model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $session = Yii::$app->session;
        $modelOrder = $session->get('modelOrder');
        if(!$modelOrder) $modelOrder = new Order();
        switch (Yii::$app->request->post('button')){
            case 'next1':
                if($modelOrder->load(Yii::$app->request->post())){
                    $BTypies = BodyType::getBodyTypies($modelOrder->id_vehicle_type, true);
                    $LTypies = LoadingType::getLoading_typies($modelOrder->id_vehicle_type);
                    $session->set('modelOrder', $modelOrder);
//                    var_dump($modelOrder->getErrors());
//                    return;
                   return $this->render('create2', [
                        'modelOrder' => $modelOrder,
                        'BTypies' => $BTypies,
                       'LTypies' => $LTypies
                    ]);
                }
                break;
            case 'next2':
                $modelOrder = $session->get('modelOrder');
                if(!$modelOrder){
                    functions::setFlashWarning('Ошибка на сервере. Попробуйте позже.');
                    return $this->redirect('create');
                }
                if($modelOrder->load(Yii::$app->request->post())) {
                    $VehicleAttributes = Vehicle::getArrayAttributes($modelOrder->id_vehicle_type, $modelOrder->body_typies);
                    $session->set('modelOrder', $modelOrder);

                    return $this->render('create3', [
                        'modelOrder' => $modelOrder,
                        'VehicleAttributes' => $VehicleAttributes
                    ]);
                }
            case 'next3':
                $modelOrder = $session->get('modelOrder');
                if(!$modelOrder){
                    functions::setFlashWarning('Ошибка на сервере. Попробуйте позже.');
                    return $this->redirect('create');
                }
                if($modelOrder->load(Yii::$app->request->post())) {

//                    var_dump($modelOrder->getErrors());
//                    return;
                    $route = new Route();
//                    $session->set('modelOrder', $modelOrder);
                    $session->set('route', $route);
                    $session->set('modelOrder', $modelOrder);
                    return $this->render('create4', [
                        'route' => $route
                    ]);
                }
                break;
            case 'next4':

                $modelOrder = $session->get('modelOrder');
                $route = $session->get('route');
                if(!$modelOrder || !$route){
                    functions::setFlashWarning('Ошибка на сервере. Попробуйте позже.');
                    return $this->redirect('create');
                }
                if($route->load(Yii::$app->request->post())) {
//                    return var_dump($modelOrder->getSuitableRates($route->distance));
                    $modelOrder->suitable_rates = $modelOrder->getSuitableRates($route->distance);
                    $TypiesPayment = ArrayHelper::map(TypePayment::find()->all(), 'id', 'type');
                    var_dump($companies = ArrayHelper::map(Yii::$app->user->identity->profile->companies, 'id', 'name'));
                    $session->set('route', $route);
                    $session->set('modelOrder', $modelOrder);

                    return $this->render('create5', [
                        'route' => $route,
                        'modelOrder' => $modelOrder,
                        'TypiesPayment' => $TypiesPayment,
                        'companies' => $companies
                    ]);
                }
                break;
            case 'next5':


                $modelOrder = $session->get('modelOrder');
                $route = $session->get('route');
                if(!$modelOrder || !$route){
                    functions::setFlashWarning('Ошибка на сервере. Попробуйте позже.');
                    return $this->redirect('create');
                }
                if($modelOrder->load(Yii::$app->request->post())) {
                    $session->set('route', $route);
                    $session->set('modelOrder', $modelOrder);
//                    return  GeoCoder::search($route->routeStart)->one()->getLocality();
                    if($route->save()) {
                        $modelOrder->id_route = $route->id;
                        $modelOrder->id_user = Yii::$app->user->id;
                        $modelOrder->scenario = Order::SCENARIO_NEW_ORDER;
                        if($modelOrder->save()) {
// Создание myaql события на изменение статуса заказа на просрочен при достижении времени valid_datetime
                            $modelOrder->setEventChangeStatusToExpired();

                            $session->remove('route');
                            $session->remove('modelOrder');
                            return $this->redirect(['client']);
                        }
//                        var_dump($modelOrder->getErrors());
//                        return var_dump($modelOrder->getErrors());
//                        return 'error_save_order';
                        functions::setFlashWarning('Ошибка на сервере. Попробуйте позже.');
                    }

                    functions::setFlashWarning('Ошибка на сервере. Попробуйте позже.');
                }
                var_dump($modelOrder->getErrors());
return 'error';
                break;

        }

        $session->remove('modelOrder');
        return $this->render('create', [
            'modelOrder' => $modelOrder,
        ]);

    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_service]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['client']);
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionAjaxChangePrice_zones(){
        echo 1;
    }

    public function actionValidateOrder()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $model = new Order();
            if($model->load(Yii::$app->request->post()))
                return \yii\widgets\ActiveForm::validate($model);
        }
        throw new \yii\web\BadRequestHttpException('Bad request!');
    }

    public function actionAcceptOrder($id_order, $id_user, $redirect = '/order/vehicle'){
        $OrderModel = Order::findOne($id_order);
        if($OrderModel->status != Order::STATUS_NEW && $OrderModel->status != Order::STATUS_IN_PROCCESSING){
            functions::setFlashWarning('Заказ был принят другим водителем.');
            return $this->redirect($redirect);
        }
        $UserModel = User::findOne($id_user);
        if(!$OrderModel || !$UserModel){
            functions::setFlashWarning('Ошибка на сервере');
            $this->redirect($redirect);
        }
        if(!$UserModel->getDrivers()->count()){
            functions::setFlashWarning('У Вас не добавлен ни один водитель!');
            return $this->redirect($redirect);
        }
        $driversArr = ArrayHelper::map($UserModel->getDrivers()->all(), 'id', 'fio');
        $vehicles = [];
        $Vehicles = $UserModel->getVehicles()->where(['in', 'status', [Vehicle::STATUS_ACTIVE, Vehicle::STATUS_ONCHECKING]])->all();
        foreach ($Vehicles as $vehicle) {
            if($vehicle->canOrder($OrderModel)) {
                $rate = PriceZone::findOne($vehicle->getMinRate($OrderModel));
                $vehicles[$vehicle->id] =
                    $vehicle->brand
                    . ' (' . $vehicle->regLicense->reg_number . ') '
                    . ' <br> '
                    . $rate->getTextWithShowMessageButton($OrderModel->route->distance)
                ;
            }
        }
        if($vehicles){
            $OrderModel->scenario = $OrderModel::SCENARIO_ACCESSING;
        }
        if($OrderModel->load(Yii::$app->request->post())){
            $OrderModel->id_pricezone_for_vehicle = Vehicle::findOne($OrderModel->id_vehicle)
                ->getMinRate($OrderModel);
            if($OrderModel->status != Order::STATUS_NEW || $OrderModel->status != Order::STATUS_IN_PROCCESSING) {
//                $OrderModel->id_vehicle = $id_user;
                $OrderModel->changeStatus(
                    Order::STATUS_VEHICLE_ASSIGNED, $OrderModel->id_user, $OrderModel->id_vehicle);
            } else  functions::setFlashWarning('Заказ только что был принят другим водителем.');

            return $this->redirect($redirect);
        }

        return $this->render('/order/accept-order', [
            'OrderModel' => $OrderModel,
            'UserModel' => $UserModel,
            'drivers' => $driversArr,
            'vehicles' => $vehicles,
            'redirect' => $redirect
        ]);
    }

    public function actionCanceledByVehicle($id_order, $id_user,  $redirect = '/order/vehicle'){
        $user = User::findOne($id_user);
        $order = Order::findOne($id_order);

        if(!$user || !$order) {
            functions::setFlashWarning('Ошибка на сервере, попробуте позже.');
            return $this->redirect($redirect);
        }

        ($order->changeStatus(Order::STATUS_IN_PROCCESSING, $order->id_user, $order->id_vehicle));
//        return;

        return $this->redirect($redirect);

    }

}
