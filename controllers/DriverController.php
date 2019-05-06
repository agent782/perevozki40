<?php

namespace app\controllers;

use app\models\DriverForm;
use app\models\DriverLicense;
use app\models\Passport;
use Yii;
use app\models\Driver;
use app\models\DriverSearch;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DriverController implements the CRUD actions for Driver model.
 */
class DriverController extends Controller
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
     * Lists all Driver models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DriverSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->where(['id_car_owner' => Yii::$app->user->id]);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Driver model.
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
     * Creates a new Driver model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($redirect = 'index', $id_car_owner)
    {
        $DriverForm = new DriverForm();
        $model = new Driver();


        if ($DriverForm->load(Yii::$app->request->post())) {
            if($DriverForm = $DriverForm->save($model, $id_car_owner)){
                Yii::$app->session->setFlash('success', 'Водитель добавлен.');
            }else {
                Yii::$app->session->setFlash('warning', 'Ошибка соединения. Попробуйте позже.');
            }
            return $this->redirect($redirect);
        } else {
            return $this->render('create', [
                'DriverForm' => $DriverForm,
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Driver model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $DriverForm = new DriverForm();
        $DriverForm = $DriverForm->initDriverForm($model);
        $DriverFormOld = $DriverForm;

        if ($DriverForm->load(Yii::$app->request->post())) {

                if($DriverForm->save($model)){
                    Yii::$app->session->setFlash('success', 'Водитель сохранен.');
                }else {
                    Yii::$app->session->setFlash('warning', 'Ошибка соединения. Попробуйте позже.');
                }


            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'DriverForm' => $DriverForm,
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Driver model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->setStatus(Driver::STATUS_DELETED);

        return $this->redirect(['index']);
    }

    /**
     * Finds the Driver model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Driver the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionRecovery($id){
        $this->findModel($id)->setStatus(Driver::STATUS_ACTIVE)
            ? Yii::$app->session->setFlash('success', 'Водитель восстановлен.')
            : Yii::$app->session->setFlash('warning', 'Ошибка соединения. Попробуйте еще раз.');
        return $this->redirect(Url::to('/driver/'));
    }

    protected function findModel($id)
    {
        if (($model = Driver::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionAjaxValidation(){
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $model = new DriverForm();
            if($model->load(Yii::$app->request->post()))
                return \yii\widgets\ActiveForm::validate($model);
        }
        throw new \yii\web\BadRequestHttpException('Bad request!');
    }
}
