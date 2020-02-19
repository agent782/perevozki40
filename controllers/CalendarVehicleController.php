<?php

namespace app\controllers;

use app\models\User;
use Yii;
use app\models\CalendarVehicle;
use app\models\CalendarVehicleSearch;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CalendarVehicleController implements the CRUD actions for CalendarVehicle model.
 */
class CalendarVehicleController extends Controller
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
     * Lists all CalendarVehicle models.
     * @return mixed
     */
    public function actionIndex($id_user = null)
    {
        if($id_user){
            $user = User::findOne($id_user);
        } else {
            $user = Yii::$app->user->identity;
        }

        $vehicles = $user->vehicles;
        $Vehicles = [];
        if($vehicles) {
            foreach ($vehicles as $vehicle) {
                $calendar = $vehicle->calendarVehicle;
                $Vehicles [$vehicle->brandAndNumber] = new ArrayDataProvider([
                    'allModels' => $calendar
                ]);
        }
        }

        return $this->render('index', [
            'Vehicles' => $Vehicles
        ]);
    }

    /**
     * Displays a single CalendarVehicle model.
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
     * Creates a new CalendarVehicle model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CalendarVehicle();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing CalendarVehicle model.
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
     * Deletes an existing CalendarVehicle model.
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
     * Finds the CalendarVehicle model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CalendarVehicle the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CalendarVehicle::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionAjaxChangeStatus(){
        $date = Yii::$app->request->post('date');
        $id_vehicle = Yii::$app->request->post('id_vehicle');
        $status = Yii::$app->request->post('status');

        $calendarVehicle = CalendarVehicle::findOne([
            'id_vehicle' => $id_vehicle,
            'date' => $date,
        ]);
        if(!$calendarVehicle) {
            $calendarVehicle = new CalendarVehicle();
//            if($status != CalendarVehicle::STATUS_FREE){
            $calendarVehicle->date = $date;
            $calendarVehicle->id_vehicle = $id_vehicle;
            $calendarVehicle->status = $status;
            if($calendarVehicle->save()) return true;
//            }
        } else {
            $calendarVehicle->status = $status;
            if($calendarVehicle->save()) return true;
        }


        return false;
    }
}
