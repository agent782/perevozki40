<?php

namespace app\controllers;

use app\components\functions\functions;
use app\models\Payment;
use app\models\User;
use Yii;
use app\models\RequestPayment;
use app\models\RequestPaymentSearch;
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
        $dataProvider->query->andFilterWhere(['id_user' => Yii::$app->user->id]);
        $dataProvider->sort->defaultOrder = [
            'create_at' => SORT_DESC
        ];
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
            //Если есть незавершенные заказы
            if($profile->hasOrdersInProccess()){
                functions::setFlashWarning('У Вас есть незавершенные заказы. Завершить их и повторить запрос.');
                return $this->redirect('/request-payment');
            }

            if($model->cost >= $min_cost && $model->cost <= $max_cost){
                functions::setFlashSuccess('Статус платежа - "В очереди..."');
                if($model->save()){
                    if($model->url_files = functions::saveImage($model,
                        'file',
                        Yii::getAlias('@requestPaymentInvoices/'),
                        $model->id
                    )) {
                        $model->save();
                    }
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

    public function actionCancel($id, $redirect = '/request-payment'){
        $model = self::findModel($id);
        if($model){
            if($model->status == $model::STATUS_NEW){
                $model->delete();
            }
        }
        return $this->redirect($redirect);
    }
}
