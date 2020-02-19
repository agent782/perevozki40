<?php

namespace app\controllers;
use app\components\functions\emails;
use app\models\Payment;
use app\models\Profile;
use app\models\setting\SettingVehicle;
use app\models\VehicleType;
use app\models\XprofileXcompany;
use function Couchbase\fastlzCompress;
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
                        'roles' => ['@'],
                        'denyCallback' => function(){
                            return 1;
                        }
                    ],
                    [
                        'allow' => true,
                        'roles' => ['?'],
                        'actions' => ['create', 'validate-order'],
//                        'denyCallback' => function(){
//                            return 1;
//                        }
                    ],
                ],
            ]
        ];
    }

    /**
     * Lists all Order models.
     * @return mixed
     */
//    public function actionIndex()
//    {
//        $searchModel = new OrderSearch();
//        $dataProvider = $searchModel->searchForClient(Yii::$app->request->queryParams);
//
//        return $this->render('index', [
//            'searchModel' => $searchModel,
//            'dataProvider' => $dataProvider,
//        ]);
//    }

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
            ->where(['in', Order::tableName().'.status', [Order::STATUS_NEW, 'status' => Order::STATUS_IN_PROCCESSING]])
            ->andWhere(['id_user' => Yii::$app->user->id]);
        $dataProvider_in_process->query
            ->where(['in', Order::tableName().'.status', [Order::STATUS_VEHICLE_ASSIGNED, Order::STATUS_DISPUTE]])
            ->andWhere(['id_user' => Yii::$app->user->id])
            ->andWhere(['!=', 'id_car_owner', Yii::$app->user->id])
        ;
        $dataProvider_arhive->query
            ->where(['in', Order::tableName().'.status', [Order::STATUS_CONFIRMED_VEHICLE, Order::STATUS_CONFIRMED_CLIENT]])
            ->andWhere(['id_user' => Yii::$app->user->id])
            ->andWhere(['!=', 'id_car_owner', Yii::$app->user->id]);
        $dataProvider_arhive->sort->defaultOrder = [
            'paid_status' => SORT_ASC,
            'datetime_finish' => SORT_DESC
        ];
        $dataProvider_expired_and_canceled->query
            ->where(['in', Order::tableName().'.status', [Order::STATUS_EXPIRED, Order::STATUS_CANCELED, Order::STATUS_NOT_ACCEPTED]])
            ->andWhere(['id_user' => Yii::$app->user->id])
//            ->andWhere(['!=', 'id_car_owner', Yii::$app->user->id])
        ;

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
            ->where(['in', Order::tableName().'.status', [Order::STATUS_VEHICLE_ASSIGNED, Order::STATUS_DISPUTE]])
            ->andWhere(['in', 'id_vehicle', Yii::$app->user->identity->vehicleids]);
        $dataProvider_arhive->query
            ->where(['in', Order::tableName().'.status', [Order::STATUS_CONFIRMED_VEHICLE, Order::STATUS_CONFIRMED_CLIENT]])
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


    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($redirect = '/client', $re = false)
    {
//        return var_dump(Yii::$app->request->post());
        $session = Yii::$app->session;
//        $modelOrder = $session->get('modelOrder');
//        if (!$modelOrder)
            $modelOrder = new Order();
        if(Yii::$app->user->can('admin') || Yii::$app->user->can('dispetcher')){
            $this->layout = 'logist';
        };

        $TypiesPayment = TypePayment::getTypiesPaymentsArray();
        $companies =[];
        $user = Yii::$app->user->identity;
        if($user) {
            $companies = ArrayHelper::map($user->profile->companies, 'id', 'name');
        }
//        }
//        else $user = User::findOne(['id' => $user_id]);

        switch (Yii::$app->request->post('button')) {
            case 'next1':
                if ($modelOrder->load(Yii::$app->request->post())) {
//                    $BTypies = BodyType::getBodyTypies($modelOrder->id_vehicle_type, true);
                    $BTypies = BodyType::getBTypiesWithShowMessageImg($modelOrder->id_vehicle_type, true);
                    $LTypies = LoadingType::getLoading_typies($modelOrder->id_vehicle_type);
                    $LTypies = LoadingType::getLTypiesWithMessageButtonImg($modelOrder->id_vehicle_type);
                    $session->set('modelOrder', $modelOrder);
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
                    $route = new Route();
//                    if($session->get('route')) $route = $session->get('route');
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
                    $user_id = ($user)?$user->id:null;
                    $modelOrder->suitable_rates = $modelOrder
                        ->getSuitableRatesCheckboxList ($route->distance, $modelOrder->getDiscount($user_id));
//                    $TypiesPayment = ArrayHelper::map(TypePayment::find()->all(), 'id', 'type');
//                    $companies = ArrayHelper::map(Yii::$app->user->identity->profile->companies, 'id', 'name');
                    $session->set('route', $route);
                    $session->set('modelOrder', $modelOrder);

                    return $this->render('create5', [
                        'route' => $route,
                        'modelOrder' => $modelOrder,
                        'TypiesPayment' => $TypiesPayment,
                        'companies' => $companies,
                        'user_id' => $user_id,
                        'redirect' => $redirect
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
                if(Yii::$app->user->isGuest) {
                    return $this->render('create5', [
                        'route' => $route,
                        'modelOrder' => $modelOrder,
                        'TypiesPayment' => $TypiesPayment,
                        'companies' => $companies,
                        'user_id' => $user_id,
                        'redirect' => $redirect
                    ]);
                }
                if ($modelOrder->load(Yii::$app->request->post())) {
                    if(!$modelOrder->selected_rates) break;
//                    return var_dump($user_id);
                    //Оформление заказа оператором
//                    if(!$user_id) {
                    if(!Profile::notAdminOrDispetcher()){
                        $user = new User();
                        $profile = new Profile();

                        $profile->scenario = Profile::SCENARIO_SAFE_SAVE;

                        $session->set('route', $route);
                        $session->set('modelOrder', $modelOrder);
                        $session->set('user', $user);
                        $session->set('profile', $profile);
//                        $session->set('modelCompany', $modelCompany);
//                        $session->set('XcompanyXprofile', $XcompanyXprofile);

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

            case 'logist_add_company':
                if (!$session->has('route')
                    ||!$session->has('modelOrder')
                    ||!$session->has('user')
                    ||!$session->has('profile'))
                {
                    functions::setFlashWarning('Ошибка на сервере. Попробуйте позже.');
                    return $this->redirect('/logist/order');
                }

                $modelOrder = $session->get('modelOrder');
                $route = $session->get('route');
                $user = $session->get('user');
                $user = new User();
                $profile = $session->get('profile');
                $modelCompany = new Company();
                $XcompanyXprofile = new XprofileXcompany();

                if ($user->load(Yii::$app->request->post()) && $profile->load(Yii::$app->request->post())) {
                    $findUser = User::findOne(['username' => $user->username]);
                    $companies = ArrayHelper::map($profile->companies, 'id', 'name');
                    if(!$findUser){
                        $user->scenario = User::SCENARIO_SAVE;
                        $user->setPassword(123456);
                        $user->generateAuthKey();
                        $user->status = User::STATUS_WAIT_ACTIVATE;
//                        return var_dump($user->getErrors());
                        if($user->save()){
                            $profile->id_user = $user->id;
                            $profile->scenario = Profile::SCENARIO_SAFE_SAVE;
                            if(!$profile->save()){
                                $user->delete();
                                functions::setFlashWarning('Ошибка на сервере. Профиль не создан. Попробуйте позже.');
                                return $this->redirect('/logist/order');
                            }
                            emails::sendAfterUserRegistration($user->id);
                        } else{
                            functions::setFlashWarning('Ошибка на сервере. Пользователь не создан. Попробуйте позже.');
                            return $this->redirect('/logist/order');
                        }
                    } else{
                        $findUser->email = $user->email;
                        $findUser->scenario = User::SCENARIO_SAVE;
                        $findProfile = $findUser->profile;
                        $findProfile->scenario = Profile::SCENARIO_SAFE_SAVE;
                        if(!$findProfile){
                            $profile->id_user = $findUser->id;
                            if(!$profile->save()){
                                functions::setFlashWarning('Ошибка на сервере. Профиль не сохранен. Попробуйте позже.');
                                return $this->redirect('/logist/order');
                            }
                        }else{
                            $findProfile->name = $profile->name;
                            $findProfile->surname = $profile->surname;
                            $findProfile->patrinimic = $profile->patrinimic;
                            $findProfile->phone2 = $profile->phone2;
                            $findProfile->email2 = $profile->email2;
                            $findProfile->sex = $profile->sex;
                        }
                        if(!$findUser->save()){
                            functions::setFlashWarning('Ошибка на сервере. Пользователь не сохранен. Попробуйте позже.');
                            return $this->redirect('/logist/order');
                        }
                        if(!$findProfile->save()){
                            return var_dump($findProfile->getErrors());

                            functions::setFlashWarning('Ошибка на сервере. Профиль не сохранен. Попробуйте позже.');
                            return $this->redirect('/logist/order');
                        }
                        $user = User::findOne(['id' => $findUser->id]);

                    }
                    if(!$route->save()){
                        functions::setFlashWarning('Ошибка на сервере. Маршрут не сохранен. Попробуйте позже.');
                        return $this->redirect('/logist/order');
                    }
                    $modelOrder->id_route = $route->id;
                    $modelOrder->id_user = $user->id;
                    $modelOrder->scenario = Order::SCENARIO_NEW_ORDER;
//                    return var_dump($user->id);
                    if($modelOrder->save()){
//                            return var_dump($modelOrder->id);
                        $session->remove('route');
                        $session->remove('modelOrder');
                        functions::setFlashSuccess('Заказ офорилен.');

                    } else {
                        functions::setFlashWarning('Ошибка на сервере. Заказ не сохранен. Попробуйте позже.');
                        return var_dump($modelOrder->getErrors());
                        return $this->redirect($redirect);
                    }
                    return $this->redirect(['/logist/order/add-company', 'id_order' => $modelOrder->id]);

                }

                return $this->render('@app/modules/logist/views/order/create',[
                    'modelOrder' => $modelOrder,
                    'route' => $route,
                    'user' => $user,
                    'profile' => $profile,
                ]);
                break;
        }

        if(Yii::$app->request->isPjax) {
            $modelOrder = $session->get('modelOrder');
            $user_id = ($user)?$user->id:null;
            $route = $session->get('route');
            $modelOrder->type_payment = Yii::$app->request->post('type_payment');
            $modelOrder->suitable_rates =
                $modelOrder->getSuitableRatesCheckboxList($route->distance, $modelOrder->getDiscount($user_id));
            if(Yii::$app->request->post('datetime_start'))$modelOrder->datetime_start =
                Yii::$app->request->post('datetime_start');
            if(Yii::$app->request->post('valid_datetime'))$modelOrder->valid_datetime =
                Yii::$app->request->post('valid_datetime');
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

            functions::setFlashWarning('Неверный запрос!');
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
        $TypiesPayment = TypePayment::find()->where(['!=','id', Payment::TYPE_SBERBANK_CARD])->all();
        foreach ($TypiesPayment as $typePayment){
            $typePayment->type = $typePayment->getTextWithIconDiscount();
        }
        $TypiesPayment = ArrayHelper::map($TypiesPayment, 'id', 'type');

        $companies = ArrayHelper::map(Profile::findOne(['id_user' => $modelOrder->id_user])->companies, 'id', 'name');
        $modelOrder->setScenarioForUpdate();

        switch (Yii::$app->request->post('button')) {
            case 'update':
                if ($modelOrder->load(Yii::$app->request->post()) && $route->load(Yii::$app->request->post())) {
//            return var_dump($modelOrder->suitable_rates);
                    if($modelOrder->id_vehicle_type == Vehicle::TYPE_SPEC){
                        $modelOrder->body_typies[1] = $modelOrder->body_typies[0];
                        unset($modelOrder->body_typies[0]);
//                        return var_dump($modelOrder->body_typies);
                    }
                    $modelOrder->suitable_rates = $modelOrder
                        ->getSuitableRatesCheckboxList($route->distance, $modelOrder->getDiscount($modelOrder->id_user));
                    $session->set('modelOrder', $modelOrder);
                    $session->set('route', $route);
                    return $this->render('/order/update2', [
                        'modelOrder' => $modelOrder,
                        'route' => $route,
                        'redirect' => $redirect
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

    public function actionAcceptOrder($id_order, $id_user, $redirect = '/order/vehicle')
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
        $Profile = $UserModel->profile;
        if (!$UserModel->getDrivers()->count() && !$Profile->is_driver) {
            functions::setFlashWarning('У Вас не добавлен ни один водитель!');
//            return $this->redirect($redirect);
        }
        $driversArr = ArrayHelper::map($UserModel->getDrivers()->all(), 'id', 'fio');
        if($UserModel->profile->is_driver){
//            return var_dump(['0' => $UserModel->profile->fioFull]);
            $driversArr['0'] = $UserModel->profile->fioFull;
        }
        $vehicles = [];
        $Vehicles = $UserModel->getVehicles()->where(['in', 'status', [Vehicle::STATUS_ACTIVE, Vehicle::STATUS_ONCHECKING]])->all();
//        return var_dump($Vehicles);

        foreach ($Vehicles as $vehicle) {
            if ($vehicle->canOrder($OrderModel)) {
//                return var_dump($vehicle->getMinRate($OrderModel));
                $rate = PriceZone::findOne($vehicle->getMinRate($OrderModel)->unique_index);
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

        if ($OrderModel->load(Yii::$app->request->post())
//            & $Profile->load(Yii::$app->request->post())
        ) {
            if($Profile->is_driver) $Profile->save(false);
            $OrderModel->id_pricezone_for_vehicle = Vehicle::findOne($OrderModel->id_vehicle)
                ->getMinRate($OrderModel)->unique_index;

            if ($OrderModel->status != Order::STATUS_NEW || $OrderModel->status != Order::STATUS_IN_PROCCESSING) {

//                return var_dump($OrderModel->id_vehicle);
                $OrderModel->changeStatus(
                    Order::STATUS_VEHICLE_ASSIGNED, $OrderModel->id_user, $OrderModel->id_vehicle);
            } else  {
                if(Profile::notAdminOrDispetcher()){
                    functions::setFlashWarning('Заказ только что был принят другим водителем.');
                } else {
                    return OK;
                }
            }


            return $this->redirect($redirect);
        }

        return $this->render('/order/accept-order', [
            'OrderModel' => $OrderModel,
            'Profile' => $Profile,
            'UserModel' => $UserModel,
            'drivers' => $driversArr,
            'vehicles' => $vehicles,
            'redirect' => $redirect,
            'id_user' => $id_user
        ]);
    }

    public function actionCanceledByVehicle($id_order, $id_user, $redirect = '/order/vehicle', $days = 3)
    {
        $user = User::findOne($id_user);
        $order = Order::findOne($id_order);
        //Нельзя отменить принятый водителем заказ спустя энное количество дней
        if((time() - strtotime($order->datetime_start)) > 60*60*24*$days && Profile::notAdminOrDispetcher()){
            functions::setFlashWarning('Прошло более ' . $days . ' дней с момента принятия заказа. Нельзя его отменить!');
            return $this->redirect($redirect);
        }
        if (!$user || !$order) {
            functions::setFlashWarning('Ошибка на сервере, попробуте позже.');
            return $this->redirect($redirect);
        }
        if($order->re){
            $order->changeStatus($order::STATUS_CANCELED, $order->id_user, $order->id_vehicle);
            return $this->redirect($redirect);
        }

        ($order->changeStatus(Order::STATUS_IN_PROCCESSING, $order->id_user, $order->id_vehicle));
//        return;

        return $this->redirect($redirect);

    }

    public function actionCanceledByClient($id_order, $id_vehicle = null, $redirect = '/order/client', $days = 3)
    {
        $order = Order::findOne($id_order);
        if (!$order) {
            functions::setFlashWarning('Ошибка на сервере, попробуте позже.');
            return $this->redirect($redirect);
        }
//Нельзя отменить принятый водителем заказ спустя энное количество дней
            if((time() - strtotime($order->datetime_start)) > 60*60*24*$days && Profile::notAdminOrDispetcher()){
                functions::setFlashWarning('Прошло более ' . $days . ' дней с момента принятия заказа. Нельзя его отменить!');
                return $this->redirect($redirect);
            }

        $order->changeStatus($order::STATUS_CANCELED, $order->id_user, $id_vehicle);
        return $this->redirect($redirect);
    }

    public function actionFinishByVehicle($id_order, $redirect = '/order/vehicle'){
        $sesssion = Yii::$app->session;

        $modelOrder = self::findModel($id_order);
        if(($modelOrder->status != Order::STATUS_VEHICLE_ASSIGNED
            || $modelOrder->id_car_owner != Yii::$app->user->id)
            && Profile::notAdminOrDispetcher()
        ){
//            return false;
            functions::setFlashWarning('Ошибка на сервере., попробуте позже.');
            return $this->redirect($redirect);
        }

        if(!$modelOrder){
            functions::setFlashWarning('Ошибка на сервере.., попробуте позже.');
            return $this->redirect($redirect);
        }
        $modelOrder->copyValueToRealValue();
        $route = $modelOrder->route;
        $realRoute = $modelOrder->realRoute;
        if(!$realRoute) {
            $realRoute = new Route();
            $realRoute->routeStart = $route->routeStart;
            $realRoute->routeFinish = $route->routeFinish;
            for ($i = 1; $i < 9; $i++) {
                $attr = 'route' . $i;
                $realRoute->$attr = $route->$attr;
            }
        }
        $modelOrder->real_datetime_start = $modelOrder->datetime_start;
        $modelOrder->real_longlength = $modelOrder->longlength;
//        $modelOrder->datetime_finish = date('d.m.Y H:i', time())
        $longlength = $modelOrder->vehicle->longlength;
        $BTypies = BodyType::getBodyTypies($modelOrder->id_vehicle_type, true);
        $LTypies = LoadingType::getLoading_typies($modelOrder->id_vehicle_type);
        $VehicleAttributes = $modelOrder->getArrayAttributesForSetFinishPricezone();
        $TypiesPayment = TypePayment::getTypiesPaymentsArray();
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
                    $modelOrder->hand_vehicle_cost = $modelOrder->getFinishCostForVehicle();
                    if(!$modelOrder->hand_vehicle_cost) $modelOrder->hand_vehicle_cost = 0;
                    $modelOrder->cost = $modelOrder->CalculateAndPrintFinishCost(false)['cost'];

                    $sesssion->set('modelOrder', $modelOrder);
                    $sesssion->set('realRoute', $realRoute);
//                    return $modelOrder->additional_cost;
//                    return $modelOrder->cost;
                    if ($realRoute->save()) {
                        $modelOrder->id_route_real = $realRoute->id;

                        return $this->render('/order/finish-by-vehicle2', [
                            'modelOrder' => $modelOrder,
                            'realRoute' => $realRoute,
                            'finishCostText' => $costAndDescription ['text'],
                            'paymrnt_typies' => $paymrnt_typies
                        ]);
                    } else {
                        functions::setFlashWarning('Ошибка на сервере, попробуйте позже.');
                        return $this->redirect($redirect);
                    }
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
                    functions::setFlashWarning('Ошибка на сервере, попробуйте позже.');
                    return $this->redirect($redirect);
                }
                if($modelOrder->load(Yii::$app->request->post())){
//                    return var_dump($modelOrder->getFinishCost(false));
                    if(!$modelOrder->cost) $modelOrder->cost =
                        $modelOrder->hand_vehicle_cost * 100 / (100 - $modelOrder->getVehicleProcentPrice());

                    if ($realRoute->save()){
                        $modelOrder->id_route_real = $realRoute->id;

                        if($modelOrder->save()){
                            if($modelOrder->ClientPaidCash) {
                                $modelOrder->paid_status = $modelOrder::PAID_YES;
                                $modelOrder->type_payment = Payment::TYPE_CASH;
                                $modelOrder->discount = $modelOrder->getDiscount($modelOrder->id_user);
                            }
//                            return var_dump($modelOrder->cost);
                            $modelOrder->changeStatus(Order::STATUS_CONFIRMED_VEHICLE, $modelOrder->id_user
                                , $modelOrder->id_vehicle);
                        } else {
                            functions::setFlashWarning('Ошибка на сервере');
                        }
                    } else  {
                        functions::setFlashWarning('Ошибка на сервере');
                    }
                    $sesssion->remove('modelOrder');
                    $sesssion->remove('realRoute');
                    return $this->redirect($redirect);
                }                        functions::setFlashWarning('Ошибка на сервере');
                break;
            default:
                break;
        }

        $sesssion->set('modelOrder', $modelOrder);
        $sesssion->set('realRoute', $realRoute);
//        return var_dump($modelOrder->longlength);
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

    public function actionReOrder($id_user = null, $redirect = '/order/vehicle'){
        if(!$id_user) $user = Yii::$app->user->identity;
        else $user = User::findOne($id_user);
        $Profile = $user->profile;
        if(!$user || !$Profile){
            functions::setFlashWarning('Ошибка на сервере. Попробуйте позже');
            return $this->redirect($redirect);
        }
        $session = Yii::$app->session;
        $modelOrder = new Order();
        $modelOrder->id_user = $user->id;
        $modelOrder->re = true;
        $realRoute = new Route();

        if (!$user->getDrivers()->count() && !$Profile->is_driver) {
            functions::setFlashWarning('У Вас не добавлен ни один водитель!');
            return $this->redirect($redirect);
        }
        $driversArr = ArrayHelper::map($user->getDrivers()->all(), 'id', 'fio');
        if($Profile->is_driver){
//            return var_dump(['0' => $UserModel->profile->fioFull]);
            $driversArr['0'] = $Profile->fioFull;
        }
        $vehicles = [];
        $Vehicles = $user->getVehicles()->where(['in', 'status', [Vehicle::STATUS_ACTIVE, Vehicle::STATUS_ONCHECKING]])->all();

        foreach ($Vehicles as $vehicle) {
            if ($vehicle) {
                $vehicles[$vehicle->id] =
                    $vehicle->brand
                    . ' (' . $vehicle->regLicense->reg_number . ') '
                    . ' <br> '
                ;
            }
        }

        switch (Yii::$app->request->post('button')) {
            case 'next1':
                $modelOrder = $session->get('modelOrder');
                $realRoute = $session->get('realRoute');
                if (!$modelOrder || !$realRoute) break;
                if ($modelOrder->load(Yii::$app->request->post())) {
                    if(!$modelOrder->id_vehicle) break;
                    $vehicle = Vehicle::findOne($modelOrder->id_vehicle);
                    $modelOrder->id_vehicle = $vehicle->id;
                    $modelOrder->id_vehicle_type = $vehicle->id_vehicle_type;
                    $modelOrder->body_typies[1] = $vehicle->bodyType->id;

                    $modelOrder->id_car_owner = $vehicle->id_user;

                    $longlength = $vehicle->longlength;
                    $modelOrder->longlength = 0;
                    $VehicleAttributes = $modelOrder->getArrayAttributesForReCreate();
                    $TypiesPayment = TypePayment::getTypiesPaymentsArray();

                    $modelOrder->scenario = Order::SCENARIO_RE_CREATE;

                    $session->set('modelOrder', $modelOrder);
                    $session->set('realRoute', $realRoute);
                    return $this->render('/order/re-order2', [
                        'modelOrder' => $modelOrder,
                        'longlength' => $longlength,
                        'VehicleAttributes' => $VehicleAttributes,
                        'realRoute' => $realRoute,
                        'TypiesPayment' => $TypiesPayment,
                        'redirect' => $redirect
                    ]);
                }
                break;
            case 'next2':
//                $modelOrder = new Order();
                $modelOrder = $session->get('modelOrder');
                $realRoute = $session->get('realRoute');
                if (!$modelOrder || !$realRoute) break;
                if ($modelOrder->load(Yii::$app->request->post())
                    && $realRoute->load(Yii::$app->request->post())
                ) {
                    $modelOrder->discount = $modelOrder->getDiscount($modelOrder->id_user);
                    $vehicle = Vehicle::findOne($modelOrder->id_vehicle);
                    if(!$modelOrder->body_typies) break;
//                    return var_dump($modelOrder->body_typies);
                    $modelOrder->selected_rates = array_keys($modelOrder->getSuitableRates
                    (
                        $realRoute->distance,
                        20
                    ));
//                    return var_dump($modelOrder->scenario);
                    if ($realRoute->save()) {
                        $modelOrder->id_route = $realRoute->id;
                        if (!$modelOrder->save()) {
                            $realRoute->delete();
                            return var_dump($modelOrder->getErrors());
                            functions::setFlashWarning('Ошибка на сервере!');
                            break;
//                            return $this->redirect($redirect);
                        }
                    } else {
                        functions::setFlashWarning('Ошибка на сервере!');
                        break;
                    }
//
                    if ($min_rate = $vehicle->getMinRate($modelOrder)) {
                        $modelOrder->id_pricezone_for_vehicle = $min_rate->unique_index;
                        $modelOrder->changeStatus(
                            Order::STATUS_VEHICLE_ASSIGNED,
                            $modelOrder->id_car_owner,
                            $modelOrder->id_vehicle);
                        functions::setFlashSuccess('Спасибо! Повторный заказ зарегистрировкан!');
                        return $this->redirect($redirect);
                    } else {
                        functions::setFlashWarning('Указанные данные по заказу не подходят для выбранного ТС');
                        return $this->render('/order/re-order', [
                            'modelOrder' => $modelOrder,
                            'driversArr' => $driversArr,
                            'vehicles' => $vehicles
                        ]);
                    }
                }
                break;
        }
        $session->set('modelOrder', $modelOrder);
        $session->set('realRoute', $realRoute);
        if(!$modelOrder){
            functions::setFlashWarning('Ошибка на сервере. Попробуйте еще раз');
            return $this->redirect('/order/re-order');
        };
        return $this->render('/order/re-order', [
            'modelOrder' => $modelOrder,
            'driversArr' => $driversArr,
            'vehicles' => $vehicles
        ]);

    }

    public function actionValidateOrder(){
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $model = new Order();
            if($model->load(Yii::$app->request->post()))
                return \yii\widgets\ActiveForm::validate($model);
        }
        throw new \yii\web\BadRequestHttpException('Bad request!');
    }
}
