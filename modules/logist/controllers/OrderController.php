<?php

namespace app\modules\logist\controllers;

use app\models\Company;
use app\models\Payment;
use app\models\Profile;
use Yii;
use app\models\Order;
use app\models\OrderSearch;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\models\TypePayment;

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
        ];
    }

    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider_newOrders = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider_in_process = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider_arhive = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider_expired_and_canceled = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider_newOrders->query
            ->where(['status' => Order::STATUS_NEW])
            ->orWhere(['status' => Order::STATUS_IN_PROCCESSING])
        ;
        $dataProvider_in_process->query
            ->where(['status' => Order::STATUS_VEHICLE_ASSIGNED])
            ->orWhere(['status' => Order::STATUS_DISPUTE]);
        $dataProvider_arhive->query
            ->where(['status' => Order::STATUS_CONFIRMED_VEHICLE])
            ->orWhere(['status' => Order::STATUS_CONFIRMED_CLIENT]);
        $dataProvider_expired_and_canceled->query
            ->where(['status' => Order::STATUS_EXPIRED])
            ->orWhere(['status' => Order::STATUS_CANCELED])
            ->orWhere(['status' => Order::STATUS_NOT_ACCEPTED]);

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
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $modelOrder = new Order();

        if ($modelOrder->load(Yii::$app->request->post())) {
            var_dump($modelOrder->id_user);
            return;
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
                          'companies' => ArrayHelper::map($profile->companies, 'id', 'name')
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
}
