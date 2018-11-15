<?php

namespace app\controllers;

use app\models\Vehicle;
use Yii;
use app\models\PriceZone;
use app\models\PriceZoneSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PriceZoneController implements the CRUD actions for PriceZone model.
 */
class PriceZoneController extends Controller
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
     * Lists all PriceZone models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PriceZoneSearch();
        $dataProviderTruck = $searchModel->search(Yii::$app->request->queryParams, Vehicle::TYPE_TRUCK, PriceZone::SORT_TRUCK);
        $dataProviderPass = $searchModel->search(Yii::$app->request->queryParams, Vehicle::TYPE_PASSENGER, PriceZone::SORT_PASS);
        $dataProviderSpec = $searchModel->search(Yii::$app->request->queryParams, Vehicle::TYPE_SPEC, PriceZone::SORT_SPEC);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProviderTruck' => $dataProviderTruck,
            'dataProviderPass' => $dataProviderPass,
            'dataProviderSpec' => $dataProviderSpec
        ]);
    }

    /**
     * Displays a single PriceZone model.
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
     * Creates a new PriceZone model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PriceZone();
        $session = Yii::$app->session;
        if($session->get('model')){
            $model = $session->get('model');
        }
            switch (Yii::$app->request->post('button')) {
                case 'next':
                    if(!$model){
                        $session->remove('model');
                        $session->setFlash('warning', 'Попробуйте еще раз или обратитесь к администратору!');
                        return $this->redirect('/price-zone/create');
                    }
                    if($model->load(Yii::$app->request->post())) {
                        $session->set('model', $model);
                        return $this->render('create2', [
                            'model' => $model,
                        ]);
                    }
                    break;
                case 'next2':
                    $model = $session->get('model');
                    if(!$model){
                        $session->remove('model');
                        $session->setFlash('warning', 'Попробуйте еще раз или обратитесь к администратору!');
//                        return var_dump($model);
                        return $this->redirect('/price-zone/create');
                    }
                    if($model->load(Yii::$app->request->post())) {
                        $session->set('model', $model);
                        return $this->render('create3', [
                            'model' => $model,
                        ]);
                    }
                    break;
                case 'next3':
                    $model = $session->get('model');
                    if(!$model){
                        $session->remove('model');
                        $session->setFlash('warning', 'Попробуйте еще раз или обратитесь к администратору!');
                        return $this->redirect('/price-zone/create');
                    }
                    if($model->load(Yii::$app->request->post()) && $model->validate()) {
                        if(!is_array($model->body_types)) $model->body_types = str_split($model->body_types, strlen($model->body_types));//Строка полученная тз радиолист привращается в массив
                        $session->set('model', $model);
//                        $model->body_types = serialize($model->body_types);
                        if($model->save()){
                            $session->remove('model');
                            $session->setFlash('success', 'Тарифная зона успешно сохранена.');
                            return $this->redirect('/price-zone');
                        }
                        return var_dump($model[errors]);
                        $session->setFlash('warning', 'Ошибка! Попробуйте еще раз или обратитесь к администратору.');
                        return $this->redirect('/price-zone');
//                        return var_dump($model[errors]);
                    }
                    else {
                        $session->set('model', $model);
                        return $this->render('create3', [
                            'model' => $model,
                        ]);
                    }
                    break;
            }
        $session->remove('model');

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing PriceZone model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) ) {
            if(!is_array($model->body_types)) $model->body_types = str_split($model->body_types, strlen($model->body_types));//Строка полученная тз радиолист привращается в массив

            if( $model->save()) {
                return $this->redirect(['index']);
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing PriceZone model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the PriceZone model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PriceZone the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PriceZone::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionValidatePriceZone(){
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $model = new PriceZone();
            if($model->load(Yii::$app->request->post()))
                return \yii\widgets\ActiveForm::validate($model);
        }
        throw new \yii\web\BadRequestHttpException('Bad request!');
    }
}
