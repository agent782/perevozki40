<?php

namespace app\modules\logist\controllers;
use app\components\functions\functions;
use app\models\Profile;
use Yii;
use app\models\Vehicle;
use app\models\VehicleSearch;
use yii\web\HttpException;


class VehicleController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $searchModel = new VehicleSearch();
        $dataProviderTruck = $searchModel->search(Yii::$app->request->queryParams, Vehicle::TYPE_TRUCK, Vehicle::SORT_TRUCK,
            [Vehicle::STATUS_ACTIVE, Vehicle::STATUS_ONCHECKING,Vehicle::STATUS_NOT_ACTIVE]);
        $dataProviderPass = $searchModel->search(Yii::$app->request->queryParams, Vehicle::TYPE_PASSENGER, Vehicle::SORT_PASS,
            [Vehicle::STATUS_ACTIVE, Vehicle::STATUS_ONCHECKING,Vehicle::STATUS_NOT_ACTIVE]);
        $dataProviderSpec = $searchModel->search(Yii::$app->request->queryParams, Vehicle::TYPE_SPEC, Vehicle::SORT_SPEC,
            [Vehicle::STATUS_ACTIVE, Vehicle::STATUS_ONCHECKING,Vehicle::STATUS_NOT_ACTIVE]);
        $dataProviderDeleted = $searchModel->search(Yii::$app->request->queryParams, [1,2,3], Vehicle::SORT_DATE_CREATE, [Vehicle::STATUS_DELETED]);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProviderTruck' => $dataProviderTruck,
            'dataProviderPass' => $dataProviderPass,
            'dataProviderSpec' => $dataProviderSpec,
            'dataProviderDeleted' => $dataProviderDeleted,
        ]);
    }

    public function actionCheck($id_vehicle)
    {
        $model = Vehicle::findOne(['id' => $id_vehicle]);
        $model->scenario = $model::SCENARIO_CHECK;
        $profile = $model->profile;

        if(!$model || !$profile){
            throw new \HttpException(404, 'Нет такого ТС или пользователя');
        }

        $model_load = false;
        switch (Yii::$app->request->post('button')) {
            case 'success':
                $model->status = $model::STATUS_ACTIVE;
                $model->error_mes = '';
                $model_load = true;
                break;
            case 'error':
                if ($model->load(Yii::$app->request->post())) {
                    $model->status = $model::STATUS_NOT_ACTIVE;
                    $model_load = true;
                }
                break;
        }
        if($model_load) {
            if ($model->save()) {
                functions::setFlashSuccess('Статус изменен. ТС ' . $model->statusText);
                functions::sendEmail(
                    [
                        $profile->email,
                        $profile->email2
                    ],
                    Yii::$app->params['robotEmail'],
                    'Изменение статуса ТС.',
                    [
                        'profile' => $profile,
                        'vehicle' => $model,
                    ],
                    [
                        'html' => 'views/changeStatusVehicle_html',
                        'text' => 'views/changeStatusVehicle_text',
                    ],
                    null
                );
            } else {
                functions::setFlashWarning('Ошибка. Статус ТС не изменен!!!');
            }
            return $this->redirect('index');
        }
        return $this->render('check', [
           'model' => $model,
            'profile' => $profile
        ]);

    }

    public function actionView($id){
        $vehicle = Vehicle::findOne($id);
        if(!$vehicle){
            throw new HttpException(404, 'ТС не найдена');
        }

        return $this->render('view', ['vehicle' => $vehicle]);
    }

}
