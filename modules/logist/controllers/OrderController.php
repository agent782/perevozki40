<?php

namespace app\modules\logist\controllers;

use app\components\functions\functions;
use app\models\Company;
use app\models\Payment;
use app\models\Profile;
use app\models\User;
use app\models\Vehicle;
use app\models\VehicleSearch;
use app\models\XprofileXcompany;
use Yii;
use app\models\Order;
use app\models\OrderSearch;
use yii\bootstrap\Html;
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
                        'roles' => ['admin', 'dispetcher']
                    ],
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
        $dataProvider_arhive->query
            ->andFilterWhere(['in', Order::tableName().'.status', [Order::STATUS_CONFIRMED_VEHICLE, Order::STATUS_CONFIRMED_CLIENT]]);
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
            Profile::findOne($modelOrder->id_user)->companies, 'id', 'name'
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

    public function actionFindVehicle($id_order, $redirect, $redirectError){
        $modelOrder = Order::findOne($id_order);
        if(!$modelOrder ){
            functions::setFlashWarning('Нет такого заказа!');
            return $this->redirect($redirectError);
        }

        $vehicles =[];
        $Vehicles = Vehicle::find()->where(['in', 'status', [Vehicle::STATUS_ACTIVE, Vehicle::STATUS_ONCHECKING]])->orderBy('id_user')->all();

        foreach ($Vehicles as $vehicle) {
            if (!$vehicle->canOrder($modelOrder)) {
                ArrayHelper::removeValue($Vehicles, $vehicle);
            }
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $Vehicles
        ]);

        return $this->render('find-vehicle', [
            'vehicles' => $vehicles,
            'dataProvider' => $dataProvider,
            'searchModel' => null,
            'modelOrder' => $modelOrder
        ]);
    }

    public function actionChangeVehicle($id_order, $id_user, $redirect = '/logist/order'){
        return 'ok';
        $modelOrder = Order::findOne($id_order);
        if(!$modelOrder ){
            functions::setFlashWarning('Нет такого заказа!');
            return $this->redirect($redirect);
        }
        $searchModel = new VehicleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $modelOrder->id_vehicle_type,
            false,[Vehicle::STATUS_ACTIVE, Vehicle::STATUS_ONCHECKING]);

        return $this->render('find-vehicle', [
//            'vehicles' => $vehicles,
            'dataProvider' => $dataProvider,
            'searchModel' => null,
            'modelOrder' => $modelOrder
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
}
