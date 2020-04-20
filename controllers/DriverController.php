<?php

namespace app\controllers;

use app\components\functions\functions;
use app\models\DriverForm;
use app\models\DriverLicense;
use app\models\Passport;
use app\models\Profile;
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
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin', 'dispetcher', 'logist', 'finance']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'create', 'ajax-validation', 'validate-driver-license'],
                        'roles' => ['car_owner', 'vip_car_owner']
                    ],
                    [
                        'actions' => ['update', 'view', 'delete', 'recovery'],
                        'allow' => true,
                        'roles' => ['car_owner', 'vip_car_owner'],
                        'matchCallback' => function ($rule, $action) {
                            $user_id = Yii::$app->user->id;
                            $driver = Driver::findOne(Yii::$app->request->get('id'));
                            if($driver) {
                                return ($driver->id_car_owner == $user_id);
                            }
                            return false;
                        },
                    ],
                ],
            ]
        ];
    }

    /**
     * Lists all Driver models.
     * @return mixed
     */
    public function actionIndex($id_user = null, $redirect = '/driver')
    {
        if(!$id_user) $id_user = Yii::$app->user->id;
        $modelProfile = Profile::findOne(['id_user' => $id_user]);
        if(!$modelProfile) return $this->redirect($redirect);

        $modelDriverLicense = $modelProfile->driverLicense;
        if(!$modelDriverLicense) $modelDriverLicense = new DriverLicense();

        $searchModel = new DriverSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->where(['id_car_owner' => $id_user]);

        if($modelProfile->load(Yii::$app->request->post()) && $modelDriverLicense->load(Yii::$app->request->post())){
            if($modelDriverLicense->save()){
                $modelProfile->id_driver_license = $modelDriverLicense->id;
                if($modelProfile->save()){
                    ($modelProfile->is_driver)
                    ? functions::setFlashSuccess('Теперь вы можете указывать себя водителем при принятии заказа')
                    : functions::setFlashSuccess('Теперь вы не можете указывать себя водителем при принятии заказа')
                    ;
                }
                else {
                    functions::setFlashWarning('Ошибка на сервере, попробуйте позже');
                }
                return $this->redirect($redirect);
            }
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'modelProfile' => $modelProfile,
            'modelDriverLicense' => $modelDriverLicense
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
    public function actionUpdate($id, $redirect = '/driver')
    {
        $model = $this->findModel($id);
        if(!$model) {
            functions::setFlashWarning('Ошибка на сервере');
            return $this->redirect($redirect);
        }
        $DriverForm = new DriverForm();
        $DriverForm = $DriverForm->initDriverForm($model);
        $DriverFormOld = $DriverForm;

        if ($DriverForm->load(Yii::$app->request->post())) {

                if($DriverForm->save($model, $model->id_car_owner)){
                    Yii::$app->session->setFlash('success', 'Водитель сохранен.');
                }else {
                    Yii::$app->session->setFlash('warning', 'Ошибка соединения. Попробуйте позже.');
                }


            return $this->redirect($redirect);
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

    public function actionValidateDriverLicense(){
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

//            $model = new VehicleForm();
            $model = new DriverLicense();
            if($model->load(Yii::$app->request->post()))
                return \yii\widgets\ActiveForm::validate($model);
        }
        throw new \yii\web\BadRequestHttpException('Bad request!');
    }
}
