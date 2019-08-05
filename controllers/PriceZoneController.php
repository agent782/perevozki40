<?php

namespace app\controllers;

use app\components\functions\functions;
use app\models\Vehicle;
use Yii;
use app\models\PriceZone;
use app\models\PriceZoneSearch;
use yii\filters\AccessControl;
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
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'validate-price-zone'],
                        'roles' => ['@', '?']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'delete'],
                        'roles' => ['admin']
                    ]
                ]
            ]
        ];
    }

    /**
     * Lists all PriceZone models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PriceZoneSearch();
        $dataProviderTruck = $searchModel->search(Yii::$app->request->queryParams,
            Vehicle::TYPE_TRUCK,
            PriceZone::SORT_TRUCK);
        $dataProviderTruck->query->andFilterWhere(['status' => PriceZone::STATUS_ACTIVE]);
        $dataProviderPass = $searchModel->search(Yii::$app->request->queryParams,
            Vehicle::TYPE_PASSENGER,
            PriceZone::SORT_PASS);
        $dataProviderPass->query->andFilterWhere(['status' => PriceZone::STATUS_ACTIVE]);
        $dataProviderSpec = $searchModel->search(Yii::$app->request->queryParams,
            Vehicle::TYPE_SPEC,
            PriceZone::SORT_SPEC);
        $dataProviderSpec->query->andFilterWhere(['status' => PriceZone::STATUS_ACTIVE]);
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
                        return $this->redirect('/price-zone/create');
                    }
                    if($model->load(Yii::$app->request->post())) {
                        switch ($model->veh_type){
                            case Vehicle::TYPE_TRUCK:
                                $model->scenario = PriceZone::SCENARIO_TRUCK;
                                break;
                            case Vehicle::TYPE_PASSENGER:
                                $model->scenario = PriceZone::SCENARIO_PASS;
                                break;
                            case Vehicle::TYPE_SPEC:
                                switch ($model->body_types){
                                    case Vehicle::BODY_manipulator:
                                        $model->scenario = PriceZone::SCENARIO_MANIPULATOR;
                                        break;
                                    case Vehicle::BODY_crane:
                                        $model->scenario = PriceZone::SCENARIO_CRANE;
                                        break;
                                    case Vehicle::BODY_dump:
                                        $model->scenario = PriceZone::SCENARIO_DUMP;
                                        break;
                                    case Vehicle::BODY_excavator:
                                        $model->scenario = PriceZone::SCENARIO_EXCAVATOR;
                                        break;
                                    case Vehicle::BODY_excavator_loader:
                                        $model->scenario = PriceZone::SCENARIO_EXCAVATOR;
                                        break;
                                }
                                break;
                        }
                        $model->id = PriceZone::getNextId();
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
                    if($model->load(Yii::$app->request->post())) {
                        if(!is_array($model->body_types)) $model->body_types = str_split($model->body_types, strlen($model->body_types));//Строка полученная тз радиолист привращается в массив
                        $session->set('model', $model);
//                        $model->body_types = serialize($model->body_types);
                        if($model->save()){
                            $session->remove('model');
                            $session->setFlash('success', 'Тарифная зона успешно сохранена.');
                            return $this->redirect('/price-zone');
                        }
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
        $modelOld = $this->findModel($id);
        switch ($modelOld->veh_type){
            case Vehicle::TYPE_TRUCK:
                $modelOld->scenario = PriceZone::SCENARIO_TRUCK;
                break;
            case Vehicle::TYPE_PASSENGER:
                $modelOld->scenario = PriceZone::SCENARIO_PASS;
                break;
            case Vehicle::TYPE_SPEC:
                switch ($modelOld->body_types[0]){
                    case Vehicle::BODY_manipulator:
                        $modelOld->scenario = PriceZone::SCENARIO_MANIPULATOR;
                        break;
                    case Vehicle::BODY_crane:
                        $modelOld->scenario = PriceZone::SCENARIO_CRANE;
                        break;
                    case Vehicle::BODY_dump:
                        $modelOld->scenario = PriceZone::SCENARIO_DUMP;
                        break;
                    case Vehicle::BODY_excavator:
                        $modelOld->scenario = PriceZone::SCENARIO_EXCAVATOR;
                        break;
                    case Vehicle::BODY_excavator_loader:
                        $modelOld->scenario = PriceZone::SCENARIO_EXCAVATOR;
                        break;
                }
                break;
        }
        $model = new PriceZone();
        $model->attributes = $modelOld->attributes;
        $model->scenario = $modelOld->scenario;
        if ($model->load(Yii::$app->request->post()) ) {
            if(PriceZone::compare($model, $modelOld)){
//                return var_dump($model->body_types);
                functions::setFlashSuccess('Вы не внесли изменений');
                return $this->redirect(['index']);
            }
            $model->unique_index = null;
            if(!is_array($model->body_types)) $model->body_types = str_split($model->body_types, strlen($model->body_types));//Строка полученная тз радиолист привращается в массив
            $modelOld->status = $modelOld::STATUS_OLD;
            $modelOld->updated_at = date('d.m.Y', time());
            if( $modelOld->save()){
                if($model->save()){
                functions::setFlashSuccess('Тариф обновлен');
                return $this->redirect(['index']);
                }
                $modelOld->id = $model->id;
                $modelOld->status = $modelOld::STATUS_ACTIVE;
                $modelOld->save();
            }
            functions::setFlashWarning('Ошибка обновления тарифа');
        }
        return $this->render('update', [
            'model' => $model,
            'isNewRecord' => false
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
        $model = $this->findModel($id);
        $model->status = PriceZone::STATUS_OLD;
        $model->save(false);
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
