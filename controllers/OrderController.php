<?php

namespace app\controllers;

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
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->searchForClient(Yii::$app->request->queryParams);

        return $this->render('client', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionVehicle(){
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->searchForVehicle(Yii::$app->request->queryParams);

        return $this->render('vehicle', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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
//        $cronJob = new CronJob();
//        $cronJob->min = '*/6';
//        $cronJob->command = Yii::getAlias('@app/yii cron/monitoring-expired-orders');
//
//        $cronTab = new CronTab();
//        $cronTab->setJobs([
//            $cronJob
//        ]);
//        $cronTab->apply();
//        $cronTab->removeAll();

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
                        if($modelOrder->save()) {
                            $session->remove('route');
                            $session->remove('modelOrder');
                            return $this->redirect(['client']);
                        }
                        var_dump($modelOrder->getErrors());

                        return 'error_save_order';
                    }

                    return 'error_save_route';
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

}
