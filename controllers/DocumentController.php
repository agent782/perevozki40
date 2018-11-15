<?php

namespace app\controllers;

use Yii;
use app\models\Document;
use app\models\DocumentSearch;
use yii\helpers\Url;
use yii\jui\Dialog;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * DocumentController implements the CRUD actions for Document model.
 */
class DocumentController extends Controller
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
     * Lists all Document models.
     * @return mixed
     */
    public function actionIndex()
    {
        $Documents = Document::find();
        $searchModel = new DocumentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'Documents' => $Documents,
        ]);
    }

    /**
     * Displays a single Document model.
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
     * Creates a new Document model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Document();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Document model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionUpload($id, $type, $completeRedirect){
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) ) {
//            if(!$model->validate()) return var_dump($model);
            $model->upload_file = UploadedFile::getInstances($model, 'upload_file');
            if(!empty($model->upload_file))
            {
//                $pdfContract = $id;
//                $model->upload_file->saveAs(Yii::getAlias('@client_contracts_uploads/'.$pdfContract.'.'.$model->upload_file->extension));

                $i = 1;
                $urlFiles = [];
                foreach ($model->upload_file as $file) {
                    switch ($type){
                        case Document::TYPE_CONTRACT_CLIENT:
                            $path = $id . '-' . $i;
                            $file->saveAs(Yii::getAlias('@client_contracts_uploads/'.$path.'.'.$file->extension));
                    }
                    $urlFiles[] = $path.'.'.$file->extension;
                    $i++;
                }

//                $model->url_upload = $pdfContract.'.'.$model->upload_file->extension;

                $model->url_upload = serialize($urlFiles);

                $model->status = Document::STATUS_ON_CHECKING;
                $model->save();
                Yii::$app->session->setFlash('success', 'Договор отправлен на проверку.');
                return $this->redirect($completeRedirect);
            }
            Yii::$app->session->setFlash('warning', 'Вы не выбрали файл. Попробуйте еще раз.');
            return $this->redirect($completeRedirect);
        }
        Yii::$app->session->setFlash('warning', 'Ошибка. Попробуйте еще раз.');
        return $this->redirect('/company/index');

    }

    // Загрузка подтвержденного договора нашим сотрудемком

    public function actionUploadConfirmDoc($id, $type)
    {
        $modelDoc = self::findModel($id);
        if(!$modelDoc) return 'ERROR';
        if ($modelDoc->load(Yii::$app->request->post())) {
            $modelDoc->confirm_file = UploadedFile::getInstance($modelDoc, 'confirm_file');
            if (!empty($modelDoc->confirm_file)) {
                switch ($type) {
                    case Document::TYPE_CONTRACT_CLIENT:
                        $path = $modelDoc->id .'.'. $modelDoc->confirm_file->extension;
                        $modelDoc->confirm_file->saveAs(Yii::getAlias('@client_contracts_confirm/' . $path));
                        $modelDoc->url_confirm = $path;
                        $modelDoc->status = Document::STATUS_SIGNED;
                        if(!$modelDoc->save()) echo 'ERROR';

                        return $this->redirect('index');
                    default:
                        return $this->redirect('index');
                }

            }
            return $this->redirect(['index']);
        }

        return $this->redirect('/company/index');

    }

    /**
     * Deletes an existing Document model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
//удаление подписанного и подтвержденного договора url_confirm
    public function actionDeleteConfirmDoc($id){
        $modelDoc = self::findModel($id);
        $path = Yii::getAlias('@client_contracts_confirm/' . $modelDoc->url_confirm);
        if(file_exists($path)){
            unlink($path);
        }
        $modelDoc->url_confirm = null;
        $modelDoc->status = Document::STATUS_UNSIGNED;
        $modelDoc->save();
        return $this->redirect('/document/index');
    }

    public function actionDeleteUploadDocs($id, $returnRedirectUrl, $type){
        $modelDoc = self::findModel($id);

        $files = [];
        switch ($modelDoc->type) {
            case Document::TYPE_CONTRACT_CLIENT:
                foreach (unserialize($modelDoc->url_upload) as $filename) {
                    $files[] = Yii::getAlias('@client_contracts_uploads/' . $filename);
                }
        }

        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
        $modelDoc->status = Document::STATUS_UNSIGNED;
        $modelDoc->comments = '';
        $modelDoc->url_upload = null;
        // При использовании в форме проверки Договора отправляется форма с комментарием о причине отказа
        if($modelDoc->load(Yii::$app->request->post())) {
            $modelDoc->status = Document::STATUS_FAILED;
        }

        $modelDoc->save();
        return $this->redirect($returnRedirectUrl);
    }

    /**
     * Finds the Document model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Document the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
//        if (($model = Document::findOne($id)) !== null) {
        if (($model = Document::find()->where(['id' => $id])->one()) !== null) {
                return $model;
        } else {
                throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionDownloadDocOnCheck($url, $type)
    {
//       echo $idCompany;

        switch ($type){
            case Document::TYPE_CONTRACT_CLIENT:

                return Yii::$app->response->sendFile(Yii::getAlias('@client_contracts_uploads/' .$url));

        }


    }

    public function actionDownloadConfirmDoc($id, $type){
        $modelDoc = Document::findOne($id);

        switch ($type) {
            case Document::TYPE_CONTRACT_CLIENT:
                return Yii::$app->response->sendFile(Yii::getAlias('@client_contracts_confirm/'.$modelDoc->url_confirm));
        }
    }


}
