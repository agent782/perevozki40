<?php

namespace app\modules\finance\controllers;

use app\models\Company;
use kartik\grid\EditableColumnAction;
use Yii;
use app\models\Order;
use app\models\OrderSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

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
        ];
        $companies = ArrayHelper::map(
            Company::find()->all(),
            'id', 'name'
        );

        if(Yii::$app->request->post('hasEditable')){
            $id_order = Yii::$app->request->post('editableKey');
//            $out = Json::encode(['output'=>'','message'=>'']);
            $Order = Order::findOne($id_order);
            if(!$Order) return 1;
            $load = Yii::$app->request->post('Order');
//            $Order->scenario = $Order::SCENARIO_UPDATE_PAID_STATUS;
            if($Order->load($load)){
                if($Order->save()){
//                    echo $out;
                    return 1;
                }
            }
            return Yii::$app->request->post('editableKey');
        }

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