<?php

namespace app\modules\logist\controllers;

use app\components\functions\functions;
use app\models\CalendarVehicle;
use app\models\Company;
use app\models\Message;
use app\models\OrdersFinishContacts;
use app\models\Payment;
use app\models\Profile;
use app\models\User;
use app\models\Vehicle;
use app\models\VehicleSearch;
use app\models\XprofileXcompany;
use kartik\grid\EditableColumnAction;
use Yii;
use app\models\Order;
use app\models\OrderSearch;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\models\TypePayment;
use app\models\Document;
use app\models\PriceZone;
use app\models\setting\SettingVehicle;
use yii\filters\AccessControl;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
{
    /**
     * {@inheritdoc}
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
                        'roles' => ['admin', 'dispetcher', 'buh']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['change-pricezone-in-proccess'],
                        'roles' => ['admin'],
                    ],
                ],
            ]
        ];
    }

    public function actions()
    {
        return ArrayHelper::merge (parent::actions(), [
            'change-pricezone-in-proccess' => [
                'class' => EditableColumnAction::class ,
                'modelClass' => Order::class ,
                'outputValue' => function (Order $model , $attribute , $key , $index) {
                    return PriceZone::findOne($model->id_pricezone_for_vehicle)->id;
                } ,
                'outputMessage' => function($model , $attribute , $key , $index) {
                    return '';
                } ,
                'scenario' => Order::SCENARIO_CHANGE_PRICEZONE_FOR_VEHICLE
            ],
            'change-datetime' => [
                'class' => EditableColumnAction::class ,
                'modelClass' => Order::class ,
                'outputValue' => function ($model , $attribute , $key , $index) {
//                    return $model->paidCarOwnerText;
                } ,
                'outputMessage' => function($model , $attribute , $key , $index) {
                    return '';
                } ,
                'scenario' => Order::SCENARIO_CHANGE_DATETIME
            ]
        ]);
    }

    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $searchModel->type_payments = [];
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider_newOrders = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider_in_process = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider_arhive = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider_expired_and_canceled = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider_newOrders->query
            ->andFilterWhere(['in', Order::tableName().'.status', [Order::STATUS_NEW, Order::STATUS_IN_PROCCESSING]]);
        $dataProvider_newOrders->sort->defaultOrder = [
            'valid_datetime' => SORT_ASC,
            'datetime_start' => SORT_ASC
        ];
        $dataProvider_in_process->query
            ->andFilterWhere(['in', Order::tableName().'.status', [Order::STATUS_VEHICLE_ASSIGNED, Order::STATUS_DISPUTE]]);
        $dataProvider_in_process->pagination = ['pageSize' => false];
        $dataProvider_arhive->query
            ->andFilterWhere(['in', Order::tableName().'.status', [Order::STATUS_CONFIRMED_VEHICLE, Order::STATUS_CONFIRMED_CLIENT]]);
        $dataProvider_arhive->pagination = ['pageSize' => 40];
        $dataProvider_expired_and_canceled->query
            ->andFilterWhere(['in', Order::tableName().'.status', [
                Order::STATUS_EXPIRED,
                Order::STATUS_CANCELED,
                Order::STATUS_NOT_ACCEPTED
            ]]);

        $dataProvider_arhive->sort->defaultOrder = [
//            'paid_status' => SORT_ASC,
            'real_datetime_start' => SORT_DESC
        ];

        $countNewOrders = Order::getCountNewOrders();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'countNewOrders' => $countNewOrders,
            'dataProvider_newOrders' => $dataProvider_newOrders,
            'dataProvider_in_process' => $dataProvider_in_process,
            'dataProvider_arhive' => $dataProvider_arhive,
            'dataProvider_canceled' => $dataProvider_expired_and_canceled
        ]);
    }

    /**
     * Displays a single Order model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
//        $orders = Order::find()->all();
//        foreach ($orders as $order){
//            if(!$order->delete()){
//                echo 0;
//            }
//        }
//        return;
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionAddCompany($id_order, $redirect = '/logist/order'){
        $modelOrder = Order::findOne($id_order);
        $modelOrder->scenario = $modelOrder::SCENARIO_ADD_ID_COMPANY;
        $companies = ArrayHelper::map(
            (!$modelOrder->re)
                ? Profile::findOne($modelOrder->id_user)->companies
                : Company::find()->orderBy('name_short')->all(),
            'id', 'name'
        );
        if($modelOrder->load(Yii::$app->request->post())){
            if($modelOrder->save()){
                functions::setFlashSuccess('Плательщик добавлен к заказу');
            } else {
//                return var_dump($modelOrder->getErrors());
                functions::setFlashWarning('Ошибка на сервере при добавлении плательщика');
            }
            return $this->redirect($redirect);
        }

        return $this->render('addCompany', [
            'modelOrder' => $modelOrder,
            'companies' => $companies,
            'redirect' => $redirect
        ]);
    }

    public function actionAutocomplete($term){
        if(Yii::$app->request->isAjax){
            $profiles = Profile::find()->all();
            $res = [];
            foreach ($profiles as $profile) {
                  if(strpos($profile->phone, $term)!==false || strpos($profile->phone2, $term)!==false){
                      $res[] = [
                          'id' => $profile->id_user,
                          'phone' => $profile->phone,
                          'phone2' => $profile->phone2,
                          'email' => $profile->email,
                          'email2' => $profile->email2,
                          'name' => $profile->name,
                          'surname' => $profile->surname,
                          'patrinimic' => $profile->patrinimic,
                          'label' => $profile->phone . ' (' . $profile->phone2 . ') ' . $profile->fioFull . ' (ID ' . $profile->id_user . ')',
                          'companies' => ArrayHelper::map($profile->companies, 'id', 'name'),
                          'info' => $profile->profileInfo . ' ' . $profile->getRating()
                      ];
                  }
            }
            echo Json::encode($res);
//        echo Json::encode([1111,22222,33333,44444]);
        }
    }

    public function actionPjaxAddCompany(){
        if(Yii::$app->request->isPjax) {
            $id_user = Yii::$app->request->post('id_user');
            $companies = ArrayHelper::map(Profile::find()->where(['id_user' => $id_user])->one()->companies, 'id', 'name');
            return $this->renderAjax('@app/modules/logist/views/order/renderCompanies', [
                'companies' => $companies,
                'modelOrder' => Yii::$app->session->get('modelOrder')
            ]);
        }
    }

    public function actionPjaxCompanyInfo(){
        if(Yii::$app->request->isPjax){
            $company = Company::findOne(Yii::$app->request->post('id_company'));
            return 'ID ' . $company->id;
        }
        return $this->redirect('/logist/order');
    }

    public function actionFindVehicle($id_order, $redirect, $redirectError, $sort = false){
        $modelOrder = Order::findOne($id_order);
        if(!$modelOrder){
            functions::setFlashWarning('Нет такого заказа!');
            return $this->redirect($redirectError);
        }
        if ($modelOrder->status != Order::STATUS_NEW && $modelOrder->status != Order::STATUS_IN_PROCCESSING) {
            functions::setFlashWarning('Заказ был принят другим водителем.');
            return $this->redirect($redirectError);
        }
        $date_day = functions::DayToStartUnixTime($modelOrder->datetime_start);
//        $date_day = date('d.m.Y', $date_day);
//        $date_day = strtotime($date_day);

        if($sort) {
            $Vehicles = $modelOrder->getSortSuitableVehicles();
        } else {
            $Vehicles = Vehicle::find()->where(['!=', 'status', [Vehicle::STATUS_DELETED, Vehicle::STATUS_FULL_DELETED]])
                ->orderBy('id_user')->all();

            foreach ($Vehicles as $vehicle) {
                if (!$vehicle->canOrder($modelOrder)) {
                    ArrayHelper::removeValue($Vehicles, $vehicle);
                }
            }
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $Vehicles,
            'pagination' => ['pageSize' => 50]
        ]);

        return $this->render('find-vehicle', [
            'dataProvider' => $dataProvider,
            'searchModel' => null,
            'modelOrder' => $modelOrder,
            'date_day' => $date_day
        ]);
    }

    public function actionChangeVehicle($id_order, $id_user, $redirect = '/logist/order'){
//        return 'ok';
        $OrderModel = Order::findOne($id_order);

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

        if ($OrderModel->load(Yii::$app->request->post())) {
            $finishContacts = $OrderModel->finishContacts;
            if(!$finishContacts) $finishContacts = new OrdersFinishContacts();
            if($finishContacts = $OrderModel->changeVehicleByFinished($OrderModel->id_vehicle,
                $OrderModel->id_driver)){
                if($finishContacts->save()){
                    if($OrderModel->save()){
                        functions::setFlashSuccess('ТС назначено');
                        return $this->redirect($redirect);
                    }
                }
            }
            functions::setFlashWarning('ОШИБКА!');
            return $this->redirect($redirect);
        }

        return $this->render('@app/views/order/accept-order', [
            'OrderModel' => $OrderModel,
            'Profile' => $Profile,
            'UserModel' => $UserModel,
            'drivers' => $driversArr,
            'vehicles' => $vehicles,
            'redirect' => $redirect,
            'id_user' => $id_user
        ]);
    }

    public function actionFinishOnlySum($id_order, $redirect = '/logist/order'){
        $order = Order::findOne($id_order);
        if(!$order) {
            functions::setFlashWarning('Заказ не найден');
            return $this->redirect();
        }
        if($order->load(Yii::$app->request->post())){
            $order->datetime_finish = $order->real_datetime_start;
            $order->cost = null;
            $order->changeStatus($order::STATUS_CONFIRMED_VEHICLE, $order->id_user, $order->id_vehicle, false);
//            if($order->save()){
//
//                functions::setFlashSuccess('Заказ №' . $order->id .' завершен и сохранен');
//            } else {
//                functions::setFlashWarning('Ощибка сохранения. Заказ не завершен');
//            }
            return $this->redirect($redirect);
        }
    }

    public function actionAjaxAlertNewOrder(){
        $post = Yii::$app->request->post();
        $id_user = $post['id_user'];
        $id_order = $post['id_order'];

//        if($id_user && $id_order) {
//            $mes = Message::find()
//                ->where(['id_order' => $id_order])
//                ->andWhere(['id_to_user' => $id_user])
//                ->andWhere(['type' => Message::TYPE_ALERT_CAR_OWNER_NEW_ORDER])
//                ->one();
//            if(!$mes) {
                $mes = new Message();
//            }
//            $mes->create_at = date('d.m.Y H:i', time());
        return $mes->AlertNewOrder($id_user, $id_order);

//        }

    }

    public function actionAjaxStartAutoFind(){
        $post = Yii::$app->request->post();
        $id_order = $post['id_order'];

        $order = Order::findOne($id_order);
        if(
            !$id_order || !$order || $order->auto_find
            || ($order->status != $order::STATUS_NEW && $order->status != $order::STATUS_IN_PROCCESSING)
        ) {
            return 0;
        }
        $order->auto_find = true;
        $order->updateAttributes(['auto_find']);
//        $order->save(false);

        functions::startCommand('console/auto-find',
            [$order->id]);
        sleep(2);
        return 1;

    }

    public function actionAjaxStopAutoFind(){
        $post = Yii::$app->request->post();
        $id_order = $post['id_order'];
        $order = Order::findOne($id_order);
        if(!$order) {
            return 0;
        }
        $order->auto_find = false;
        $order->save(false);
        return 1;
    }

    public function actionHide(){
        $post = Yii::$app->request->post();
        $id_order = $post['id_order'];
        $order = Order::findOne($id_order);
        if($order->hide) {
            $order->hide = false;
        } else {
            $order->hide = true;
        }
        if($order->save(false)){
            return ($order->hide) ? 1 : 0;
        }
        return false;
    }

    public function actionFreeVehicles($ids){
        $dataProvider = new ActiveDataProvider([
           'query' => Vehicle::find()->where(['in', 'id', unserialize($ids)])
        ]);

        return $this->render('free-vehicles', ['dataProvider' => $dataProvider]);
    }

    public function actionAjaxResetAutoFind(){
        $post = Yii::$app->request->post();
        $id_order = $post['id_order'];
        $order = Order::findOne($id_order);
        if(!$order) {
            return 0;
        }
        $order->alert_car_owner_ids = null;
        $order->save(false);
        return 1;
    }

}
