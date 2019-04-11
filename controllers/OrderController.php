<?php

namespace app\controllers;
use app\models\Payment;
use app\models\Profile;
use app\models\setting\SettingVehicle;
use app\models\VehicleType;
use app\models\XprofileXcompany;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use app\models\Message;
use app\models\User;
use app\models\Company;
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
                        'roles' => ['car_owner', 'client', 'admin', 'dispetcher']
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

    public function actionClient()
    {
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
            ->where(['in', 'status', [Order::STATUS_VEHICLE_ASSIGNED, Order::STATUS_DISPUTE]])
            ->andWhere(['id_user' => Yii::$app->user->id]);
        $dataProvider_arhive->query
            ->where(['in', 'status', [Order::STATUS_CONFIRMED_VEHICLE, Order::STATUS_CONFIRMED_CLIENT]])
            ->andWhere(['id_user' => Yii::$app->user->id]);
        $dataProvider_arhive->sort->defaultOrder = [
            'paid_status' => SORT_ASC,
            'datetime_finish' => SORT_DESC
        ];
        $dataProvider_expired_and_canceled->query
            ->where(['in', 'status', [Order::STATUS_EXPIRED, Order::STATUS_CANCELED, Order::STATUS_NOT_ACCEPTED]])
            ->andWhere(['id_user' => Yii::$app->user->id]);

        return $this->render('client', [
            'searchModel' => $searchModel,
            'dataProvider_newOrders' => $dataProvider_newOrders,
            'dataProvider_in_process' => $dataProvider_in_process,
            'dataProvider_arhive' => $dataProvider_arhive,
            'dataProvider_canceled' => $dataProvider_expired_and_canceled
        ]);
    }

    public function actionVehicle()
    {
        $searchModel = new OrderSearch();
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider_newOrders = $searchModel->searchCanVehicle(Yii::$app->request->queryParams);
        $dataProvider_in_process = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider_arhive = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider_in_process->query
            ->where(['in', 'status', [Order::STATUS_VEHICLE_ASSIGNED, Order::STATUS_DISPUTE]])
            ->andWhere(['in', 'id_vehicle', Yii::$app->user->identity->vehicleids]);
        $dataProvider_arhive->query
            ->where(['in', 'status', [Order::STATUS_CONFIRMED_VEHICLE, Order::STATUS_CONFIRMED_CLIENT]])
            ->andWhere(['in', 'id_vehicle', Yii::$app->user->identity->vehicleids]);
        $dataProvider_arhive->sort->defaultOrder = [
            'paid_status' => SORT_ASC,
            'datetime_finish' => SORT_DESC
        ];

        return $this->render('vehicle', [
            'searchModel' => $searchModel,
            'dataProvider_newOrders' => $dataProvider_newOrders,
            'dataProvider_in_process' => $dataProvider_in_process,
            'dataProvider_arhive' => $dataProvider_arhive,
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
    public function actionCreate($user_id = null)
    {
        if(Yii::$app->user->can('admin') || Yii::$app->user->can('dispetcher')){
//            $this->layout = 'logist';
        };
        $session = Yii::$app->session;
        $modelOrder = $session->get('modelOrder');
        if (!$modelOrder) $modelOrder = new Order();
        $TypiesPayment = TypePayment::getTypiesPaymentsArray();
        $companies =[];
        if($user_id){
            $user = Yii::$app->user->identity;
            $companies = ArrayHelper::map($user->profile->companies, 'id', 'name');
        }
//        else $user = User::findOne(['id' => $user_id]);

        switch (Yii::$app->request->post('button')) {
            case 'next1':
                if ($modelOrder->load(Yii::$app->request->post())) {
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
                if (!$modelOrder) {
                    functions::setFlashWarning('Ошибка на сервере. Попробуйте позже.');
                    return $this->redirect('create');
                }
                if ($modelOrder->load(Yii::$app->request->post())) {
                    $VehicleAttributes = Vehicle::getArrayAttributes($modelOrder->id_vehicle_type, $modelOrder->body_typies);
                    $session->set('modelOrder', $modelOrder);

                    return $this->render('create3', [
                        'modelOrder' => $modelOrder,
                        'VehicleAttributes' => $VehicleAttributes
                    ]);
                }
            case 'next3':
                $modelOrder = $session->get('modelOrder');
                if (!$modelOrder) {
                    functions::setFlashWarning('Ошибка на сервере. Попробуйте позже.');
                    return $this->redirect('create');
                }
                if ($modelOrder->load(Yii::$app->request->post())) {

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
                if (!$modelOrder || !$route) {
                    functions::setFlashWarning('Ошибка на сервере. Попробуйте позже.');
                    return $this->redirect('create');
                }
                if ($route->load(Yii::$app->request->post())) {
//                    if(!$user_id) $user_id = Yii::$app->user->id;
//                    $modelOrder->type_payment = Payment::TYPE_CASH ;
                    $modelOrder->suitable_rates = $modelOrder->getSuitableRatesCheckboxList ($route->distance, $modelOrder->getDiscount($user_id));
//                    $TypiesPayment = ArrayHelper::map(TypePayment::find()->all(), 'id', 'type');
//                    $companies = ArrayHelper::map(Yii::$app->user->identity->profile->companies, 'id', 'name');
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

                if (!$modelOrder || !$route) {
                    functions::setFlashWarning('Ошибка на сервере. Попробуйте позже.');
                    return $this->redirect('create');
                }

                if ($modelOrder->load(Yii::$app->request->post())) {

                    //Оформление заказа оператором
                    if(!$user_id) {
                        $user = new User();
                        $profile = new Profile();
//                        $modelCompany = new Company();
//                        $XcompanyXprofile = new XprofileXcompany();

                        $session->set('route', $route);
                        $session->set('modelOrder', $modelOrder);
                        $session->set('user', $user);
                        $session->set('profile', $profile);

                        return $this->render('@app/modules/logist/views/order/create',[
                            'modelOrder' => $modelOrder,
                            'route' => $route,
                            'user' => $user,
                            'profile' => $profile,
//                            'modelCompany' => $modelCompany,
//                            'XcompanyXprofile' => $XcompanyXprofile
                        ]);
                    }
                    $session->set('route', $route);
                    $session->set('modelOrder', $modelOrder);
//                    return  GeoCoder::search($route->routeStart)->one()->getLocality();
                    if ($route->save()) {
                        $modelOrder->id_route = $route->id;
                        $modelOrder->id_user = Yii::$app->user->id;
                        $modelOrder->scenario = Order::SCENARIO_NEW_ORDER;
                        if ($modelOrder->save()) {
// Создание myaql события на изменение статуса заказа на просрочен при достижении времени valid_datetime
                            $modelOrder->setEventChangeStatusToExpired();

                            $session->remove('route');
                            $session->remove('modelOrder');
                            return $this->redirect(['client']);
                        }
                        functions::setFlashWarning('Ошибка на сервере. Попробуйте позже.');
                    }

                    functions::setFlashWarning('Ошибка на сервере. Попробуйте позже.');
                }
                functions::setFlashWarning('Ошибка на сервере. Попробуйте позже.');
//                var_dump($modelOrder->getErrors());
//                return 'error';
                break;
            case 'logist_set_user':
                $route = $session->get('route');
                $modelOrder = $session->get('modelOrder');
                $user = $session->get('user');
                $profile = $session->get('profile');

                if(!$route || !$modelOrder || !$user || !$profile ){
                    functions::setFlashWarning('Ошибка на сервере');
                    return $this->redirect('/logist/order/create');
                }
                if($user->load(Yii::$app->request->post()) && $profile->load(Yii::$app->request->post())){

                    if(User::find()->where(['username' => $user->username])->one()){
                        $tmpUser = User::findOne(['username' => $user->username]);
                        $tmpUser->email = $user->email;
                        $tmpProfile = $tmpUser->profile;
                        $tmpProfile->name = $profile->name;
                        $tmpProfile->surname = $profile->surname;
                        $tmpProfile->patrinimic = $profile->patrinimic;
                        $tmpProfile->phone2 = $profile->phone2;
                        $tmpProfile->email2 = $profile->email2;

                        $user = $tmpUser;
                        $profile = $tmpProfile;
                    } else {
                        $user->setPassword(rand(10000000, 99999999));
                        $user->generateAuthKey();
                    }
                    $user->scenario = $user::SCENARIO_SAVE;
                    $profile->scenario = $profile::SCENARIO_SAFE_SAVE;

                    if ($route->save() && $user->save()) {
                        $profile->id_user = $user->id;
                        $profile->save();
                        $modelOrder->id_route = $route->id;
                        $modelOrder->id_user = $user->id;
                        $modelOrder->scenario = Order::SCENARIO_LOGIST_NEW_ORDER;
                        if ($modelOrder->save()) {
// Создание myaql события на изменение статуса заказа на просрочен при достижении времени valid_datetime
                            $modelOrder->setEventChangeStatusToExpired();

                            $session->remove('route');
                            $session->remove('modelOrder');
                            if($modelOrder->type_payment != Payment::TYPE_BANK_TRANSFER){
//                                return var_dump($modelOrder->type_payment);
                                return $this->redirect('/logist/order');
                            } else {
                                return $this->redirect(Url::to(['/logist/order/add-company', 'id_order' => $modelOrder->id]));
//
                            }
                        }
                        functions::setFlashWarning('Ошибка на сервере. Попробуйте позже.');
//                        return var_dump($modelOrder->getErrors());
                        return $this->redirect('/logist/order');
                    }


                    return var_dump($profile->getErrors());
                }

                break;
            case 'logist_set_company':

                break;

        }


        if(Yii::$app->request->isPjax) {
            $modelOrder = $session->get('modelOrder');
            $route = $session->get('route');
            $modelOrder->type_payment = Yii::$app->request->post('type_payment');
            $modelOrder->suitable_rates = $modelOrder->getSuitableRatesCheckboxList($route->distance, $modelOrder->getDiscount($user_id));
            if(Yii::$app->request->post('datetime_start'))$modelOrder->datetime_start = Yii::$app->request->post('datetime_start');
            if(Yii::$app->request->post('valid_datetime'))$modelOrder->valid_datetime = Yii::$app->request->post('valid_datetime');
            $session->set('modelOrder', $modelOrder);
            return $this->renderAjax('selectedRates', [
                'modelOrder' => $modelOrder,
            ]);
        }
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
    public function actionUpdate($id_order, $redirect = '/order/client')
    {
        $session = Yii::$app->session;
        $modelOrder = $this->findModel($id_order);
        if (!$modelOrder ) {
            functions::setFlashWarning('Ошибка на сервере');
            return $this->redirect($redirect);
        }
        if($modelOrder->status == Order::STATUS_VEHICLE_ASSIGNED
            || $modelOrder->status == Order::STATUS_CONFIRMED_VEHICLE
            || $modelOrder->status == Order::STATUS_CONFIRMED_CLIENT
            || $modelOrder->status == Order::STATUS_DISPUTE){

            functions::setFlashWarning('Нельзя изменить этот заказ!');
            return $this->redirect($redirect);
        }


        $route = Route::findOne($modelOrder->id_route);
        if (!$route){
            functions::setFlashWarning('Ошибка на сервере');
            return $this->redirect($redirect);
        }
        If($modelOrder->status == Order::STATUS_EXPIRED || $modelOrder->status == Order::STATUS_CANCELED){
            $modelOrder->datetime_start = null;
            $modelOrder->valid_datetime = null;
        }


        $BTypies = BodyType::getBodyTypies($modelOrder->id_vehicle_type, true);
        $LTypies = LoadingType::getLoading_typies($modelOrder->id_vehicle_type);
        // Для функции  Vehicle::getArrayAttributes() .... для получения атрибутов для спецтехники она выбтрае 1й, а не 0й элемент массива с типами кузовов
        $tmpBodyTypies[1] = $modelOrder->body_typies[0];
        $VehicleAttributes = Vehicle::getArrayAttributes($modelOrder->id_vehicle_type, $tmpBodyTypies);
        $TypiesPayment = TypePayment::find()->all();
        foreach ($TypiesPayment as $typePayment){
            $typePayment->type = $typePayment->getTextWithIconDiscount();
        }
        $TypiesPayment = ArrayHelper::map($TypiesPayment, 'id', 'type');

        $companies = ArrayHelper::map(Yii::$app->user->identity->profile->companies, 'id', 'name');
        $modelOrder->setScenarioForUpdate();

        switch (Yii::$app->request->post('button')) {
            case 'update':
                if ($modelOrder->load(Yii::$app->request->post()) && $route->load(Yii::$app->request->post())) {
//            return var_dump($modelOrder->suitable_rates);
                    $modelOrder->suitable_rates = $modelOrder
                        ->getSuitableRatesCheckboxList($route->distance, $modelOrder->getDiscount($modelOrder->id_user));
                    $session->set('modelOrder', $modelOrder);
                    $session->set('route', $route);
                    return $this->render('/order/update2', [
                        'modelOrder' => $modelOrder,
                        'route' => $route
                    ]);
                }
                break;
            case 'update2':
                $modelOrder = $session->get('modelOrder');
                $route = $session->get('route');
                if(!$modelOrder || !$route){
                    functions::setFlashWarning('Ошибка на сервере');
                    $session->remove('modelOrder');
                    $session->remove('route');
                    return $this->redirect($redirect);
                }
                if ($modelOrder->load(Yii::$app->request->post())) {
                    if ($route->save() && $modelOrder->save()) {
                        $modelOrder->changeStatus(Order::STATUS_NEW, $modelOrder->id_user);
                    } else {
                        functions::setFlashWarning('Ошибка на сервере');
                    }
                    $session->remove('modelOrder');
                    $session->remove('route');
                    return $this->redirect($redirect);
                    break;
                }
            case 'back':
                $modelOrder = $session->get('modelOrder');
                $route = $session->get('route');
                return $this->render('update', [
                    'modelOrder' => $modelOrder,
                    'BTypies' => $BTypies,
                    'LTypies' => $LTypies,
                    'VehicleAttributes' => $VehicleAttributes,
                    'TypiesPayment' => $TypiesPayment,
                    'companies' => $companies,
                    'route' => $route,
                    'redirect' => $redirect
                ]);
        }

        return $this->render('update', [
            'modelOrder' => $modelOrder,
            'BTypies' => $BTypies,
            'LTypies' => $LTypies,
            'VehicleAttributes' => $VehicleAttributes,
            'TypiesPayment' => $TypiesPayment,
            'companies' => $companies,
            'route' => $route,
            'redirect' => $redirect
        ]);
    }

    /**
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id, $redirect)
    {
        $this->findModel($id)->delete();

        return $this->redirect([$redirect]);
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

    public function actionValidateOrder()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $model = new Order();
            if ($model->load(Yii::$app->request->post()))
                return \yii\widgets\ActiveForm::validate($model);
        }
        throw new \yii\web\BadRequestHttpException('Bad request!');
    }

    public function actionAcceptOrder($id_order, $id_user = null, $redirect = '/order/vehicle')
    {
        $OrderModel = Order::findOne($id_order);
        if ($OrderModel->status != Order::STATUS_NEW && $OrderModel->status != Order::STATUS_IN_PROCCESSING) {
            functions::setFlashWarning('Заказ был принят другим водителем.');
            return $this->redirect($redirect);
        }
        $UserModel = User::findOne($id_user);
        if (!$OrderModel || !$UserModel) {
            functions::setFlashWarning('Ошибка на сервере');
            $this->redirect($redirect);
        }
        if (!$UserModel->getDrivers()->count()) {
            functions::setFlashWarning('У Вас не добавлен ни один водитель!');
            return $this->redirect($redirect);
        }
        $driversArr = ArrayHelper::map($UserModel->getDrivers()->all(), 'id', 'fio');
        $vehicles = [];
        $Vehicles = $UserModel->getVehicles()->where(['in', 'status', [Vehicle::STATUS_ACTIVE, Vehicle::STATUS_ONCHECKING]])->all();
        foreach ($Vehicles as $vehicle) {
            if ($vehicle->canOrder($OrderModel)) {
                $rate = PriceZone::findOne($vehicle->getMinRate($OrderModel));
                $rate = $rate->getWithDiscount(SettingVehicle::find()->limit(1)->one()->price_for_vehicle_procent);
                $vehicles[$vehicle->id] =
                    $vehicle->brand
                    . ' (' . $vehicle->regLicense->reg_number . ') '
                    . ' <br> '
                    . $rate->getTextWithShowMessageButton($OrderModel->route->distance, true);
            }
        }
        if ($vehicles) {
            $OrderModel->scenario = $OrderModel::SCENARIO_ACCESSING;
        }
        if ($OrderModel->load(Yii::$app->request->post())) {
            $OrderModel->id_pricezone_for_vehicle = Vehicle::findOne($OrderModel->id_vehicle)
                ->getMinRate($OrderModel);
            if ($OrderModel->status != Order::STATUS_NEW || $OrderModel->status != Order::STATUS_IN_PROCCESSING) {
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

    public function actionCanceledByVehicle($id_order, $id_user, $redirect = '/order/vehicle')
    {
        $user = User::findOne($id_user);
        $order = Order::findOne($id_order);

        if (!$user || !$order) {
            functions::setFlashWarning('Ошибка на сервере, попробуте позже.');
            return $this->redirect($redirect);
        }

        ($order->changeStatus(Order::STATUS_IN_PROCCESSING, $order->id_user, $order->id_vehicle));
//        return;

        return $this->redirect($redirect);

    }

    public function actionCanceledByClient($id_order, $id_vehicle = null, $redirect = '/order/client')
    {
        $order = Order::findOne($id_order);
        if (!$order) {
            functions::setFlashWarning('Ошибка на сервере, попробуте позже.');
            return $this->redirect($redirect);
        }

        $order->changeStatus($order::STATUS_CANCELED, $order->id_user, $id_vehicle);
        return $this->redirect($redirect);
    }

    public function actionFinishByVehicle($id_order, $redirect = '/order/vehicle'){
        $sesssion = Yii::$app->session;

        $modelOrder = self::findModel($id_order);

        if(!$modelOrder){
            functions::setFlashWarning('Ошибка на сервере, попробуте позже.');
            return $this->redirect($redirect);
        }
        $modelOrder->copyValueToRealValue();
        $route = $modelOrder->route;
        $realRoute = new Route();
        $realRoute->routeStart = $route->routeStart;
        $realRoute->routeFinish = $route->routeFinish;
        for($i=1;$i<9;$i++) {
            $attr = 'route' . $i;
            $realRoute->$attr = $route->$attr;
        }

        $modelOrder->real_datetime_start = $modelOrder->datetime_start;
//        $modelOrder->datetime_finish = date('d.m.Y H:i', time())
        $longlength = $modelOrder->vehicle->longlength;
        $BTypies = BodyType::getBodyTypies($modelOrder->id_vehicle_type, true);
        $LTypies = LoadingType::getLoading_typies($modelOrder->id_vehicle_type);
        $VehicleAttributes = $modelOrder->getArrayAttributesForSetFinishPricezone();
        $TypiesPayment = ArrayHelper::map(TypePayment::find()->all(), 'id', 'type');
        $companies = ArrayHelper::map(Yii::$app->user->identity->profile->companies, 'id', 'name');
        $paymrnt_typies = ArrayHelper::map(TypePayment::find()->all(),'id', 'type');
        $modelOrder->setScenarioForFinish();

        switch (Yii::$app->request->post('button')){
            case 'next':
                if($sesssion->get('modelOrder')) $modelOrder = $sesssion->get('modelOrder');
                if($sesssion->get('realRoute')) $realRoute = $sesssion->get('realRoute');
                if(!$modelOrder || !$realRoute){
                    functions::setFlashWarning('Ошибка на сервере, попробуте позже.');
                    return $this->redirect($redirect);
                }
                if($modelOrder->load(Yii::$app->request->post()) && $realRoute->load(Yii::$app->request->post())) {
                    $modelOrder->id_price_zone_real = $modelOrder->getFinishPriceZone();
                    $costAndDescription = $modelOrder->CalculateAndPrintFinishCost(true, true);
                    $modelOrder->cost = $modelOrder->CalculateAndPrintFinishCost(false)['cost'];
                    $sesssion->set('modelOrder', $modelOrder);
                    $sesssion->set('realRoute', $realRoute);

                    return $this->render('/order/finish-by-vehicle2', [
                        'modelOrder' => $modelOrder,
                        'realRoute' => $realRoute,
                        'finishCostText' => $costAndDescription ['text'],
                        'paymrnt_typies' => $paymrnt_typies
                    ]);
                }
                break;
            case 'back':
                if($sesssion->get('modelOrder')) $modelOrder = $sesssion->get('modelOrder');
                if($sesssion->get('realRoute')) $realRoute = $sesssion->get('realRoute');
                if(!$modelOrder || !$realRoute){
                    functions::setFlashWarning('Ошибка на сервере, попробуте позже.');
                    return $this->redirect($redirect);
                }
//                $BTypies = BodyType::getBodyTypies($modelOrder->id_vehicle_type, true);
//                $LTypies = LoadingType::getLoading_typies($modelOrder->id_vehicle_type);
//                $VehicleAttributes = $modelOrder->getArrayAttributesForSetFinishPricezone();
//                $TypiesPayment = ArrayHelper::map(TypePayment::find()->all(), 'id', 'type');
//                $companies = ArrayHelper::map(Yii::$app->user->identity->profile->companies, 'id', 'name');


                return $this->render('/order/finish-by-vehicle',[
                    'modelOrder' => $modelOrder,
                    'realRoute' => $realRoute,
                    'BTypies' => $BTypies,
                    'LTypies' => $LTypies,
                    'VehicleAttributes' => $VehicleAttributes,
                    'TypiesPayment' => $TypiesPayment,
                    'companies' => $companies,
                    'redirect' => $redirect,
                    'longlength' => $longlength
                ]);
                break;
            case 'finish':
                if($sesssion->get('modelOrder')) $modelOrder = $sesssion->get('modelOrder');
                if($sesssion->get('realRoute')) $realRoute = $sesssion->get('realRoute');
                if(!$modelOrder || !$realRoute){
                    functions::setFlashWarning('Ошибка на сервере, попробуте позже.');
                    return $this->redirect($redirect);
                }
                if($modelOrder->load(Yii::$app->request->post())){
                    if ($realRoute->save()){
                        $modelOrder->id_route_real = $realRoute->id;
                        if($modelOrder->save()){
                            $modelOrder->changeStatus(Order::STATUS_CONFIRMED_VEHICLE, $modelOrder->id_user, $modelOrder->id_vehicle);
                        } else {
                            functions::setFlashWarning('Ошибка на сервере');
                        }
                    } else  {
                        functions::setFlashWarning('Ошибка на сервере');
                    }
                    $sesssion->remove('modelOrder');
                    $sesssion->remove('realRoute');
                    return $this->redirect($redirect);
                }
                break;
            default:
                break;
        }

        $sesssion->set('modelOrder', $modelOrder);
        $sesssion->set('realRoute', $realRoute);

        return $this->render('/order/finish-by-vehicle',[
            'modelOrder' => $modelOrder,
            'realRoute' => $realRoute,
            'BTypies' => $BTypies,
            'LTypies' => $LTypies,
            'VehicleAttributes' => $VehicleAttributes,
            'TypiesPayment' => $TypiesPayment,
            'companies' => $companies,
            'redirect' => $redirect,
            'longlength' => $longlength
        ]);
    }

}
