<?php

namespace app\modules\finance\controllers;

use app\components\functions\functions;
use app\models\Payment;
use Yii;
use app\models\RequestPayment;
use app\models\RequestPaymentSearch;
use yii\web\Controller;
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
    public function actionCreate()
    {
        $model = new RequestPayment();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
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

    public function actionApply($id, $redirect = '/finance/request-payment'){
        $modelRequestPayment = RequestPayment::findOne($id);
        if(!$modelRequestPayment){
            functions::setFlashWarning('Нет такой заявки на выплату');
            return $this->redirect($redirect);
        }
        if($modelRequestPayment->status == RequestPayment::STATUS_OK){
            functions::setFlashInfo('Заявка уже выполнена!');
            return $this->redirect($redirect);
        }
        $modelPayment = new Payment();
        $modelPayment->id_user = $modelRequestPayment->id_user;
        $modelPayment->cost = $modelRequestPayment->cost;
        $modelPayment->calculation_with = Payment::CALCULATION_WITH_CAR_OWNER;
        $modelPayment->type = $modelRequestPayment->type_payment;
        $modelPayment->direction = Payment::CREDIT;
        $modelPayment->status = Payment::STATUS_SUCCESS;
        $modelPayment->comments = $modelRequestPayment->requisites;
        $modelPayment->id_request_payment = $modelRequestPayment->id;

        if($modelPayment->save()){
            $modelRequestPayment->status = RequestPayment::STATUS_OK;
            $modelRequestPayment->save();
            functions::setFlashSuccess('Платеж проведен');
        } else {
            return var_dump($modelPayment->getErrors());
            functions::setFlashWarning('Платеж не проведен');
        }
        return $this->redirect($redirect);

    }
}
