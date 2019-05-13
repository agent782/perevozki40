<?php

namespace app\modules\finance\controllers;

use app\components\functions\functions;
use app\models\Company;
use app\models\OrderSearch;
use app\models\Profile;
use app\models\setting\SettingFinance;
use Yii;
use app\models\Payment;
use app\models\PaymentSearch;
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
        $model = new Payment();
        $model->date = date('d.m.Y');
        $model->id_implementer = Yii::$app->user->id;
//        $model->id_our_company = SettingFinance::getIdDefaultCompany();
//        $model->status = $model::STATUS_SUCCESS;
//        $model->calculation_with = $model::CALCULATION_WITH_CLIENT;
//        $model->direction = $model::DEBIT;
//        $model->type = Payment::TYPE_BANK_TRANSFER;
        $companies = Company::getArrayForAutoComplete();
        $profiles = Profile::getArrayForAutoComplete();
//        if ($model->load(Yii::$app->request->post())) {
//            if($model->save()){
//                functions::setFlashSuccess('Платеж проведен.');
//            } else {
//                functions::setFlashWarning('Платеж не проведен.');
//            }
//            return $this->redirect(['index', 'id' => $model->id]);
//        }
        switch (Yii::$app->request->post('button')){
            case 'select':
                if($model->load(Yii::$app->request->post())) {
                    $searchModel = new OrderSearch();
                    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                    $visiblePaid = false;
                    $visiblePaidCarOwner = false;
                    $visibleInvoice = false;
                    switch ($model->calculation_with){
                        case $model::CALCULATION_WITH_CLIENT:
                            $visiblePaid = true;
                            $visibleInvoice = true;
                            $dataProvider->query->andWhere(['id_user' => $model->id_user]);

                            break;
                        case $model::CALCULATION_WITH_CAR_OWNER:
                            $visiblePaidCarOwner = true;

                            $dataProvider->query->andWhere(['id_car_owner' => $model->id_user]);
                            break;
                    }

                    return $this->render('select_orders', [
                        'dataProvider' => $dataProvider,
                        'searchModel' => $searchModel,
                        'visiblePaid' => $visiblePaid,
                        'visiblePaidCarOwner' => $visiblePaidCarOwner,
                        'visibleInvoice' => $visibleInvoice
                    ]);
                }
                break;
            case 'save':
                break;

        }

        return $this->render('create', [
            'model' => $model,
            'companies' => $companies,
            'profiles' => $profiles
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Payment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }

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
}
