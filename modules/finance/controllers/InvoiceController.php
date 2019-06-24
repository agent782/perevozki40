<?php

namespace app\modules\finance\controllers;

use app\components\functions\emails;
use app\components\functions\functions;
use app\models\Order;
use Yii;
use app\models\Invoice;
use app\models\InvoiceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * InvoiceController implements the CRUD actions for Invoice model.
 */
class InvoiceController extends Controller
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
     * Lists all Invoice models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InvoiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Invoice model.
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
     * Creates a new Invoice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Invoice();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Invoice model.
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
     * Deletes an existing Invoice model.
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
     * Finds the Invoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Invoice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Invoice::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionValidateDate()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $model = new Invoice();
            if($model->load(Yii::$app->request->post()))
                return \yii\widgets\ActiveForm::validate($model);
        }
        throw new \yii\web\BadRequestHttpException('Bad request!');
    }

    public function actionUpload($type, $id_order, $redirect){
        $modelOrder = Order::findOne($id_order);
        if(!$modelOrder){
            functions::setFlashWarning('Заказ не найден.');
            return $this->redirect($redirect);
        }
        $modelInvoice = null;
        switch ($type) {
            case Invoice::TYPE_INVOICE:
                $modelInvoice = $modelOrder->invoice;
                break;
            case Invoice::TYPE_CERTIFICATE:
                $modelInvoice = $modelOrder->certificate;
                break;
        }
        if(!$modelInvoice) {
            $modelInvoice = new Invoice();
            $modelInvoice->id_order = $id_order;
        }
        $path = '';
        $filename = '';
        if($modelInvoice->load(Yii::$app->request->post())){
            switch ($type){
                case Invoice::TYPE_INVOICE:
                    $modelInvoice->type = Invoice::TYPE_INVOICE;
                    $path = Yii::getAlias('@invoices/');
                    $filename = 'perevozki40_schet_';
                    break;
                case Invoice::TYPE_CERTIFICATE:
                    $modelInvoice->type = Invoice::TYPE_CERTIFICATE;
                    $path = Yii::getAlias('@certificates/');
                    $filename = 'perevozki40_akt_';
                    break;
                default:
                    functions::setFlashWarning('Ошибка. Неверный тип документв.');
                    return $this->redirect($redirect);
            }
            $filename .= $modelInvoice->number
                . '_'
                . str_replace('.', '', $modelInvoice->date )
                . '_' . functions::translit($modelOrder->company->name)
            ;
            if(file_exists($path . $modelInvoice->url) && is_file($path . $modelInvoice->url)){
                unlink($path . $modelInvoice->url);
            }
            if($modelInvoice->url = functions::saveImage($modelInvoice, 'upload_file', $path, $filename)){
                $modelInvoice->upload_file = null;
                if($modelInvoice->save()){
                    emails::sendAfterUploadInvoice($modelOrder->id_user, $modelInvoice, $modelOrder->id, [$path.$modelInvoice->url]);
                    functions::setFlashSuccess('Документ загружен');
                    return $this->redirect($redirect);
                }
            }
        }
        functions::setFlashWarning('Ошибка загрузки файла.');
        return $this->redirect($redirect);



    }

    public function actionDownload(string $pathToFile, string $redirect){
        return functions::DownloadFile($pathToFile, $redirect);
//        Yii::$app->response->xSendFile($pathToFile);
    }
}
