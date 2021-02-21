<?php

namespace app\modules\finance\controllers;

use app\components\functions\functions;
use app\models\Company;
use app\models\Invoice;
use app\models\Message;
use app\models\Order;
use app\models\OrderSearch;
use app\models\Profile;
use app\models\setting\SettingFinance;
use Yii;
use app\models\Payment;
use app\models\PaymentSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PaymentController implements the CRUD actions for Payment model.
 */
class PaymentController extends Controller
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
                        'roles' => ['admin', 'buh']
                    ]
                ]
            ]
        ];
    }

    /**
     * Lists all Payment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = [
            'pageSize' => 100
        ];
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Payment model.
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
     * Creates a new Payment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (Yii::$app->request->isPjax){
            $id_company = Yii::$app->request->post('id_company');
            $form_id = Yii::$app->request->post('form_id');
            $model = new Payment();

            $invoices_not_paid = ArrayHelper::map(Company::findOne(
                $id_company)->getInvoices(),
                'id_order',
                'labelInvoices');

            return $this->renderAjax('chkboxlist', [
                'i' => $id_company,
                'form_id' => $form_id,
                'model' => $model,
                'invoices' => $invoices_not_paid
            ]);
        }
        $model = new Payment();
        $model->date = date('d.m.Y');
        $model->id_implementer = Yii::$app->user->id;
        $model->id_our_company = SettingFinance::find()->one()->id_default_company;
        $model->status = $model::STATUS_SUCCESS;
        $model->calculation_with = $model::CALCULATION_WITH_CLIENT;
        $model->direction = $model::DEBIT;
        $model->type = Payment::TYPE_BANK_TRANSFER;
        $companies = Company::getArrayForAutoComplete();
        $profiles = Profile::getArrayForAutoComplete();
        $our_companies = ArrayHelper::map(Yii::$app->user->identity->profile->companies, 'id', 'name');
        if ($model->load(Yii::$app->request->post())) {
//            return var_dump($model);

            if($model->save()){
                if($selected_orders = $model->invoices){
                    foreach ($selected_orders as $order_id){
                        $order = Order::findOne($order_id);
                        if($order->paid_status != Order::PAID_YES) {
                            $order->paid_status = Order::PAID_YES;
                            $order->scenario = Order::SCENARIO_CHANGE_PAID_STATUS;
                            $order->date_paid = $model->date;
                            if($order->save()){
//                                $order->sendMesAfterChangePaidStatus();
                                Message::sendPushToUser(
                                    1,
                                    '№' . $order->id . ' оплачен клиентом'
                                );
                            }
                        }
                    }
                }

                functions::setFlashSuccess('Платеж проведен.');
            } else {
                functions::setFlashWarning('Платеж не проведен.');
            }
            return $this->redirect('index');
        }

        return $this->render('create', [
            'model' => $model,
            'companies' => $companies,
            'profiles' => $profiles,
            'our_companies' => $our_companies
        ]);
    }

    /**
     * Updates an existing Payment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $companies = Company::getArrayForAutoComplete();
        $profiles = Profile::getArrayForAutoComplete();
        $our_companies = ArrayHelper::map(Yii::$app->user->identity->profile->companies, 'id', 'name');

        if ($model->load(Yii::$app->request->post())) {
            if($model->save()){
                functions::setFlashSuccess('Платеж проведен.');
            } else {
                functions::setFlashWarning('Платеж не проведен.');
            }
            return $this->redirect('index');
        }

        return $this->render('update', [
            'model' => $model,
            'companies' => $companies,
            'profiles' => $profiles,
            'our_companies' => $our_companies
        ]);
    }

    /**
     * Deletes an existing Payment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = $model::STATUS_CANCELED;

        if($model->save()){
            functions::setFlashSuccess('Платеж отменен.');
        } else {
            functions::setFlashWarning('Ошибка. Платеж не отменен');
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the Payment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Payment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Payment::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionValidatePayment()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $model = new Payment();
            if($model->load(Yii::$app->request->post()))
                return \yii\widgets\ActiveForm::validate($model);
        }
        throw new \yii\web\BadRequestHttpException('Bad request!');
    }

    public function actionChkboxlistInvoices(){
        if (Yii::$app->request->isPjax){
            $id_company = Yii::$app->request->post('id_company');
            $form_id = Yii::$app->request->post('form_id');
            return $this->renderAjax('chkboxlist', [
                'i' => $id_company,
                'form_id' => $form_id
            ]);
        }
    }
}
