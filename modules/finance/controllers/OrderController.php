<?php

namespace app\modules\finance\controllers;

use app\components\functions\functions;
use app\models\Company;
use kartik\grid\EditableColumnAction;
use Yii;
use app\models\Order;
use app\models\OrderSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions() {

        return ArrayHelper::merge ( parent::actions () , [
            'pru' => [
                'class' => EditableColumnAction::class ,
                'modelClass' => Order::class ,
                'outputValue' => function ($model , $attribute , $key , $index) {
                    return $model->paidText;
                } ,
                'outputMessage' => function($model , $attribute , $key , $index) {
                    return '';
                } ,
                'scenario' => Order::SCENARIO_CHANGE_PAID_STATUS
            ],
            'changeDatePaid' => [
                'class' => EditableColumnAction::class ,
                'modelClass' => Order::class ,
                'outputValue' => function ($model , $attribute , $key , $index) {
                    return $model->date_paid;
                } ,
                'outputMessage' => function($model , $attribute , $key , $index) {
                    return '';
                } ,
                'scenario' => Order::SCENARIO_CHANGE_PAID_STATUS
            ],
            'changePaidCarOwnerStatus' => [
                'class' => EditableColumnAction::class ,
                'modelClass' => Order::class ,
                'outputValue' => function ($model , $attribute , $key , $index) {
                    return $model->paidCarOwnerText;
                } ,
                'outputMessage' => function($model , $attribute , $key , $index) {
                    return '';
                } ,
            ],
            'changePaymentType' => [
                'class' => EditableColumnAction::class ,
                'modelClass' => Order::class ,
                'outputValue' => function (Order $model , $attribute , $key , $index) {
                    $model->discount = $model->getDiscount($model->id_user);
                    $model->cost_finish = $model->getFinishCost();
                    $model->cost_finish_vehicle = $model->getFinishCostForVehicle();
                    $model->save();
                    return $model->paymentMinText;
                } ,
                'outputMessage' => function($model , $attribute , $key , $index) {
                    return '';
                },
                'scenario' => Order::SCENARIO_CHANGE_TYPE_PAYMENT
            ],
            'set_avans_client' => [
                'class' => EditableColumnAction::class ,
                'modelClass' => Order::class ,
                'outputValue' => function ($model , $attribute , $key , $index) {
                    return $model->avans_client;
                } ,
                'outputMessage' => function($model , $attribute , $key , $index) {
                    return '';
                } ,
                'scenario' => Order::SCENARIO_CHANGE_AVANS_CLIENT
            ]

        ]);
    }

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
                        'roles' => ['admin', 'buh', 'dispetcher']
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
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['in', Order::tableName().'.status', [Order::STATUS_CONFIRMED_VEHICLE, Order::STATUS_CONFIRMED_CLIENT]]);
        $dataProvider->sort->defaultOrder = [
//            'paid_status' => SORT_ASC,
            'datetime_finish' => SORT_DESC,
        ];
        $dataProvider->pagination = [
            'pageSize' => 100
        ];
        $companies = ArrayHelper::map(
            Company::find()->all(),
            'id', 'name'
        );

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'companies' => $companies
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
        $model = new Order();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
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
}
