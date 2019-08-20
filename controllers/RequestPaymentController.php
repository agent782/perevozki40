<?php

namespace app\controllers;

use app\components\functions\functions;
use app\models\User;
use Yii;
use app\models\RequestPayment;
use app\RequestPaymentSearch;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RequestPaymentController implements the CRUD actions for RequestPayment model.
 */
class RequestPaymentController extends Controller
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
     * Lists all RequestPayment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RequestPaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RequestPayment model.
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
     * Creates a new RequestPayment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id_user = null, $redirect = '/request-payment')
    {
        $user = Yii::$app->user->identity;
        if($id_user) $user = User::findOne($id_user);

        if(!$user){
            throw new HttpException('Ошибка на сервере');
        }
        $profile = $user-> profile;
        $model = new RequestPayment();
        $model->id_user = $user->id;
        $min_cost = 1000;
        $sum_request_payments = $profile->getSumRequestsPayments();
        $max_cost = $profile->getBalanceCarOwner()['balance'] - $sum_request_payments;


        if ($model->load(Yii::$app->request->post())) {
            if($model->cost >= $min_cost && $model->cost <= $max_cost){
                functions::setFlashSuccess('Статус платежа - "В очереди..."');
                if($model->save()){
                    return $this->redirect('/request-payment');
                }

            } else {
                if($model->cost < $min_cost) {
                    functions::setFlashWarning('Минимальная сумма для получения  - ' . $min_cost . ' р.');
                }
                if($model->cost > $max_cost) {
                    functions::setFlashWarning('Превышена максимальная сумма для выплаты!');
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'max_cost' => $max_cost,
            'min_cost' => $min_cost
        ]);
    }

    /**
     * Updates an existing RequestPayment model.
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
     * Deletes an existing RequestPayment model.
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
     * Finds the RequestPayment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RequestPayment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RequestPayment::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
