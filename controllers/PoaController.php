<?php

namespace app\controllers;

use app\components\widgets\ShowMessageWidget;
use app\models\DownloadPoaForm;
use PhpOffice\PhpWord\Shared\ZipArchive;
use Yii;
use app\models\XprofileXcompany;
use app\models\SearchXprofileXcompany;
use kartik\alert\Alert;
use kartik\alert\AlertBlock;
use yii\bootstrap\Modal;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Company;
use yii\web\UploadedFile;

/**
 * PoaController implements the CRUD actions for XprofileXcompany model.
 */
class PoaController extends Controller
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
     * Lists all XprofileXcompany models.
     * @return mixed
     */
    public function actionIndex()
    {
        $POA = XprofileXcompany::find();

        $searchModel = new SearchXprofileXcompany();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'POA' => $POA
        ]);
    }

    /**
     * Displays a single XprofileXcompany model.
     * @param integer $id_profile
     * @param integer $id_company
     * @return mixed
     */
    public function actionView($id_profile, $id_company)
    {
        $model = $this->findModel($id_profile, $id_company);
        if(!$model)
            new \HttpException(404, 'Нет такой доверенности.');
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new XprofileXcompany model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new XprofileXcompany();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id_profile' => $model->id_profile, 'id_company' => $model->id_company]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing XprofileXcompany model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id_profile
     * @param integer $id_company
     * @return mixed
     */
    public function actionUpdate($id_profile, $id_company)
    {
        $model = $this->findModel($id_profile, $id_company);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id_profile' => $model->id_profile, 'id_company' => $model->id_company]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing XprofileXcompany model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id_profile
     * @param integer $id_company
     * @return mixed
     */
    public function actionDelete($id_profile, $id_company)
    {
        $this->findModel($id_profile, $id_company)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the XprofileXcompany model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id_profile
     * @param integer $id_company
     * @return XprofileXcompany the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id_profile, $id_company)
    {
        if (($model = XprofileXcompany::findOne(['id_profile' => $id_profile, 'id_company' => $id_company])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionDownloadForm($id_company, $id_profile, $return)
    {
        $modelPOA =
            XprofileXcompany::find()
            ->where(['id_profile' => $id_profile])
            ->andWhere(['id_company' => $id_company])
            ->one()
        ;

        if (!$modelPOA) {
            \Yii::$app->session->setFlash('warning', 'Ошибка! Попробуйте еще раз или обратитесь к администратору.');
            return $this->redirect($return);
        }
        if($modelPOA->load(Yii::$app->request->post())) {
            if ($modelPOA = $modelPOA->createPowerOfAttorneyForm()) {
                if ($modelPOA->save())
                    return \Yii::$app->response->sendFile(\Yii::getAlias('@poa_forms/' . $modelPOA->url_form));
                else \Yii::$app->session->setFlash('warning', 'Ошибка! Попробуйте еще раз или обратитесь к администратору.');
            } else $this->redirect($return);
        }
//        return var_dump($modelPOA);
        return $this->redirect($return);
    }

    public function actionDownloadConfirmPoa($idCompany, $idProfile, $return){
        $modelPOA = XprofileXcompany::find()->where(['id_company' => $idCompany, 'id_profile' => $idProfile])->one();
        $file = Yii::getAlias('@poa_upload/' . $modelPOA->url_poa);
       if(file_exists($file)){
            $return /Yii::$app->response->sendFile($file)->send();
        }

        return $this->redirect($return);
    }

    public function actionUploadClientPoa($idCompany, $idUser, $completeRedirect)
    {
        $model = XprofileXcompany::findByCompanyAndUser($idCompany, $idUser);

//        if($modelPOA->load(Yii::$app->request->post())){
        if ($model->load(Yii::$app->request->post())) {
            $model->upload_file = UploadedFile::getInstance($model, 'upload_file');
            if (!empty($model->upload_file)) {
                if (!empty($model->url_upload_poa)) $model = $model->DeleteUploadPoaFiles();
                if(!$model->id || !$model->term_of_office) {

                    Yii::$app->session->setFlash('warning', 'СНАЧАЛА СКАЧАЙТЕ ПОЖАЛУЙСТА БЛАНК!');
//
                    return $this->redirect($completeRedirect);
                }
//                $i = 1;
//                $urlFiles = [];
//                foreach ($model->upload_file as $file) {
//                    $path = $model->id_profile . '-' . $model->id_company . '-' . $i;
//                    $file->saveAs(Yii::getAlias('@poa_upload/' . $path . '.' . $file->extension));
//
//                    $urlFiles[] = $path . '.' . $file->extension;
//                    $i++;
//                }

                $path = $model->id_profile . '-' . $model->id_company . '-' .time();
                $model->upload_file->saveAs(Yii::getAlias('@poa_upload/' . $path . '.' . $model->upload_file->extension));


//                $model->url_upload_poa = serialize($urlFiles);

                $model->url_upload_poa = $path . '.' . $model->upload_file->extension;

                $model->STATUS_POA = XprofileXcompany::STATUS_POWER_OF_ATTORNEY_ON_CHECKING;
                $model->comments = '';
                $model->save();
                Yii::$app->session->setFlash('success', 'Доверенность отправлена на проверку.');
                return $this->redirect($completeRedirect);
            }
            Yii::$app->session->setFlash('warning', 'Вы не выбрали файл. Попробуйте еще раз.');
            return $this->redirect($completeRedirect);
        }
        Yii::$app->session->setFlash('warning', 'Ошибка. Попробуйте еще раз.');
        return $this->redirect($completeRedirect);
    }

    public function actionDownloadPoaOnCheck($url){
        return Yii::$app->response->sendFile(Yii::getAlias('@poa_upload/' .$url));

    }

    public function actionDeleteUploadPoa($idCompany, $idUser, $redirect)
    {
//        $modelPOA = Company::find()->where(['id' =>$idCompany])->one()->xprofileXcompany;
        $modelPOA = XprofileXcompany::findByCompanyAndUser($idCompany, $idUser);
        if($modelPOA && $modelPOA = $modelPOA->DeleteUploadPoaFile()){
            return $this->redirect($redirect);
        }

//        $modelPOA->url_upload_poa = '';
//        $modelPOA->STATUS_POA = XprofileXcompany::STATUS_POWER_OF_ATTORNEY_UNSIGNED;
//        if (!$modelPOA->save()) return $this->redirect('/');
        return $this->redirect('/');
    }

    public function actionValidateDate()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $model = new DownloadPoaForm();
            if($model->load(Yii::$app->request->post()))
                return \yii\widgets\ActiveForm::validate($model);
        }
        throw new \yii\web\BadRequestHttpException('Bad request!');
    }

    public function actionConfirmPoa($id_company, $id_user, $return){
        $modelPOA = XprofileXcompany::find()->where(['id_profile' => $id_user, 'id_company' => $id_company])->one();
        $modelPOA->STATUS_POA = XprofileXcompany::STATUS_POWER_OF_ATTORNEY_SIGNED;
        $modelPOA->url_poa = $modelPOA->url_upload_poa;
        if($modelPOA->save() && $modelPOA->url_poa) {
            if($modelPOA->save()) Yii::$app->session->setFlash('info', 'Статус доверенности изменен.');
        } else {
            Yii::$app->session->setFlash('warning', 'ОШИБКА. Попробуйте еще раз.');
        }
        return $this->redirect($return);
    }

    public function actionErrorPoa($id_company, $id_user, $return)
    {
        $modelPOA = XprofileXcompany::find()->where(['id_profile' => $id_user, 'id_company' => $id_company])->one();

        if ($modelPOA->load(Yii::$app->request->post())) {
            if(($modelPOA->DeleteUploadPoaFile())) {
                $modelPOA->STATUS_POA = XprofileXcompany::STATUS_POWER_OF_ATTORNEY_FAILED;
                if($modelPOA->save()) {
                    Yii::$app->session->setFlash('info', 'Статус доверенности изменен.');
                    return $this->redirect($return);
                }
            }
        }
        Yii::$app->session->setFlash('warning', 'ОШИБКА. Попробуйте еще раз.');
        return $this->redirect($return);
    }

    public function actionDeleteAllPoaFiles($idCompany, $idProfile, $return){
        $modelPOA = XprofileXcompany::find()->where(['id_company' => $idCompany, 'id_profile' => $idProfile])->one();
        if($modelPOA->DeleteConfirmPoa()->DeleteUploadPoaFile()){
            Yii::$app->session->setFlash('success', 'Операция выполнена успешно');
        } else Yii::$app->session->setFlash('warning', 'Ошибка выполнения');

        return $this->redirect($return);
    }
}
