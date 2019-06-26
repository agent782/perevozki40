<?php

namespace app\modules\logist\controllers;
use app\components\functions\functions;
use app\models\Profile;
use Yii;
use app\models\Vehicle;
use app\models\VehicleSearch;


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
            'SearchModel' => $searchModel,
            'dataProviderTruck' => $dataProviderTruck,
            'dataProviderPass' => $dataProviderPass,
            'dataProviderSpec' => $dataProviderSpec,
            'dataProviderDeleted' => $dataProviderDeleted,
        ]);
    }

    public function actionCheck($id, $id_user)
    {
        $model = Vehicle::findOne(['id' => $id]);
        $model->scenario = $model::SCENARIO_CHECK;
        $profile = Profile::findOne(['id_user' => $id_user]);


        switch (Yii::$app->request->post('button')) {
            case 'success':
                $model->status = $model::STATUS_ACTIVE;
                $model->error_mes = '';
                break;
            case 'error':
                if ($model->load(Yii::$app->request->post())) {
                    $model->status = $model::STATUS_NOT_ACTIVE;
                }
                break;
        }
        if ($model->save()) {
            functions::setFlashSuccess('Статус изменен. ТС ' . $model->statusText);
            functions::sendEmail(
                $profile->user->email,
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
            return var_dump($model->getErrors());
            functions::setFlashWarning('Ошибка. Статус ТС не изменен!!!');
        }
        return $this->redirect('index');

    }

}
