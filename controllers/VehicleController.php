<?php

namespace app\controllers;

use app\components\functions\functions;
use app\models\Order;
use app\models\Profile;
use app\models\RegLicense;
use app\models\VehicleForm;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use function Sabre\Uri\parse;
use Symfony\Component\CssSelector\Tests\Parser\ReaderTest;
use Yii;
use app\models\Vehicle;
use app\models\PriceZone;
use app\models\VehicleSearch;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\LoadingType;
use yii\filters\AccessControl;

use yii\web\UploadedFile;

/**
 * VehicleController implements the CRUD actions for Vehicle model.
 */
class VehicleController extends Controller
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
                        'roles' => ['car_owner', 'admin', 'dispetcher']
                    ]
                ],
            ]
        ];
    }

    /**
     * Lists all Vehicle models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VehicleSearch();
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProviderTruck = $searchModel->search(Yii::$app->request->queryParams, Vehicle::TYPE_TRUCK, Vehicle::SORT_TRUCK, [Vehicle::STATUS_ACTIVE, Vehicle::STATUS_ONCHECKING]);
        $dataProviderPass = $searchModel->search(Yii::$app->request->queryParams, Vehicle::TYPE_PASSENGER, Vehicle::SORT_PASS, [Vehicle::STATUS_ACTIVE, Vehicle::STATUS_ONCHECKING]);
        $dataProviderSpec = $searchModel->search(Yii::$app->request->queryParams, Vehicle::TYPE_SPEC, Vehicle::SORT_SPEC, [Vehicle::STATUS_ACTIVE, Vehicle::STATUS_ONCHECKING]);
        $dataProviderDeleted = $searchModel->search(Yii::$app->request->queryParams, [1,2,3], Vehicle::SORT_DATE_CREATE, [Vehicle::STATUS_DELETED]);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProviderTruck' => $dataProviderTruck,
            'dataProviderPass' => $dataProviderPass,
            'dataProviderSpec' => $dataProviderSpec,
            'dataProviderDeleted' => $dataProviderDeleted,
        ]);
    }



    /**
     * Displays a single Vehicle model.
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
     * Creates a new Vehicle model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id_user, $redirect = '/vehicle/index')
    {
        $modelVehicle = new Vehicle(['scenario' => Vehicle::SCENARIO_CREATE]);
        $VehicleForm = new VehicleForm();
        $modelRegLicense = new RegLicense();

        $session = Yii::$app->session;
        switch (Yii::$app->request->post('button')){
            case 'create_next1':
                if($session['VehicleForm'])$VehicleForm=$session->get('VehicleForm');

                if($VehicleForm->load(Yii::$app->request->post())) {
                    $session->set('VehicleForm', $VehicleForm);
                    return $this->render('create2', [
                        'modelVehicle' => $modelVehicle,
                        'VehicleForm' => $VehicleForm
                    ]);
                }
                break;
            case 'create_back1':
                $session->remove('VehicleForm');
                return $this->redirect($redirect);
                break;
            case 'create_back3':
                if($session['VehicleForm']){
                    $VehicleForm=$session->get('VehicleForm');
                    return $this->render('create2', [
                        'modelRegLicense' => $modelRegLicense,
                        'VehicleForm' => $VehicleForm
                    ]);
                }

                break;
            case 'create_next2':
                if($session['VehicleForm'])$VehicleForm=$session->get('VehicleForm');

                if($VehicleForm->load(Yii::$app->request->post())) {

                    $modelRegLicense = new RegLicense(['id_user' => $id_user]);
                    $session->set('VehicleForm', $VehicleForm);
                    return $this->render('createFinish', [
                        'modelRegLicense' => $modelRegLicense,
                        'VehicleForm' => $VehicleForm,
                        'id_user' => $id_user
                    ]);
                } else return 'no';
                break;
            case 'create_finish':
                if($session->get('VehicleForm'))$VehicleForm=$session->get('VehicleForm');
                if($VehicleForm->load(Yii::$app->request->post()) && $modelRegLicense->load(Yii::$app->request->post())) {
                    $session->set('VehicleForm', $VehicleForm);
                    $session->set('modelRegLicense', $modelRegLicense);
//                    return var_dump($modelRegLicense);
//                    return  $modelRegLicense->reg_number . ' ' .  Vehicle::find()->where(['id_user' => Yii::$app->user->id])->one()->regLicense->reg_number;
//                    echo $modelVehicle->id_vehicle_type;
//                    return var_dump($VehicleForm);
//                    var_dump($modelRegLicense);

                    if($modelRegLicense->save()) {
                        $modelRegLicense->image1 = functions::saveImage(
                            $modelRegLicense,
                            'image1',
                            Yii::getAlias('@photo_reg_license/'),
                            $modelRegLicense->id . '_1'
                        );
                        $modelRegLicense->image2 = functions::saveImage(
                            $modelRegLicense,
                            'image2',
                            Yii::getAlias('@photo_reg_license/'),
                            $modelRegLicense->id . '_2'
                        );
                        $modelRegLicense->save();
                        if ($modelVehicle = $VehicleForm->saveVehicle($modelRegLicense->id, $id_user)) {
                            $session->remove('VehicleForm');

                            functions::setFlashSuccess('ТС создано и отправлено на модерацию.');

                            functions::sendEmail(
                              [
                                  Yii::$app->params['adminEmail']['email'],
                                  Yii::$app->params['logistEmail']['email'],
                              ],
                              null,
                              'Новое ТС!',
                              [
                                  'vehicle' => $modelVehicle,
                                  'profile' => Profile::findOne(['id_user' => $modelVehicle->id_user]),
                              ],

                              [
                                  'html' => 'views/toAdmin/VehicleOnCheck',
                                  'text' => 'views/toAdmin/VehicleOnCheck',
                              ]
                            );

                            return $this->redirect($redirect);
                        }
//                        return var_dump($VehicleForm);
                    }
                    functions::setFlashWarning('Ощибка сервера. ВУ не сохранено.');
                    return $this->redirect($redirect);
                }

                break;
        }

        if($session['VehicleForm'])$VehicleForm=$session->remove('VehicleForm');
        return $this->render('create', [
                'modelVehicle' => $modelVehicle,
                'VehicleForm' => $VehicleForm,
            ]);
    }

    /**
     * Updates an existing Vehicle model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $redirect = '/vehicle')
    {
        $model = $this->findModel($id);
        if(!$model){
            functions::setFlashWarning('Такого ТС не существует.');
            return $this->redirect($redirect);
        }
        if($model->hasOrder(Order::STATUS_VEHICLE_ASSIGNED)){
            functions::setFlashWarning('У этого ТС есть незавершенные заказы! Нельзя редактировать это ТС. Прежде завершите заказ.');
            return $this->redirect($redirect);
        }

        $modelOLD = $model;
        if (!$model)
            throw new HttpException(404, 'ТС не найдено!');
        switch ($model->id_vehicle_type){
            case $model::TYPE_TRUCK:
                $model->scenario = $model::SCENARIO_UPDATE_TRUCK;
                break;
            case $model::TYPE_PASSENGER:
                $model->scenario = $model::SCENARIO_UPDATE_PASS;
                break;
            case $model::TYPE_SPEC:
                switch ($model->body_type){
                    case $model::BODY_manipulator:
                        $model->scenario = $model::SCENARIO_UPDATE_SPEC_BODY_manipulator;
                        break;
                    case $model::BODY_dump:
                        $model->scenario = $model::SCENARIO_UPDATE_SPEC_BODY_dump;
                        break;
                    case $model::BODY_crane:
                        $model->scenario = $model::SCENARIO_UPDATE_SPEC_BODY_crane;
                        break;
                    case $model::BODY_excavator:
                        $model->scenario = $model::SCENARIO_UPDATE_SPEC_BODY_excavator;
                        break;
                    case $model::BODY_excavator_loader:
                        $model->scenario = $model::SCENARIO_UPDATE_SPEC_BODY_excavator;
                        break;
                }
                break;
        }
        $modelRegLicense = $model->regLicense;
        $modelRegLicense->id_user = $model->id_user;
        $modelRegLicenseOLD = $modelRegLicense;
        if (!$modelRegLicense) $modelRegLicense = new RegLicense();

        if ($model->load(Yii::$app->request->post()) && $modelRegLicense->load(Yii::$app->request->post())) {

            if ($model->validate() && $modelRegLicense->validate()) {

                $model->unlinkAll('loadingtypes', true);
                $model->unlinkAll('price_zones', true);
                $model->update_at = date('d.m.Y H:i:s');
                $model->status = $model::STATUS_ONCHECKING;

                foreach ($model->loadingTypeIds as $loadingTypeId) {
                    $loadingType = LoadingType::find()->where(['id' => $loadingTypeId])->one();
                    $model->link('loadingtypes', $loadingType);
                }
                foreach ($model->Price_zones as $price_zone) {
                    $PriceZone = PriceZone::find()->where(['id' => $price_zone])->one();
                    $model->link('price_zones', $PriceZone);
                }

                $tmpPhoto = functions::saveImage($model,
                    'photoFile',
                    Yii::getAlias('@photo_vehicle/'),
                    $model->id
                );
                if ($tmpPhoto) $model->photo = $tmpPhoto;

                $tmpImage1 = functions::saveImage(
                    $modelRegLicense,
                    'image1File',
                    Yii::getAlias('@photo_reg_license/'),
                    $modelRegLicense->id . '_1'
                );
                if ($tmpImage1) $modelRegLicense->image1 = $tmpImage1;
                $tmpImage2 = functions::saveImage(
                    $modelRegLicense,
                    'image2File',
                    Yii::getAlias('@photo_reg_license/'),
                    $modelRegLicense->id . '_2'
                );
                if ($tmpImage2) $modelRegLicense->image2 = $tmpImage2;
                if ($model->save() && $modelRegLicense->save()) {
                    Yii::$app->session->setFlash('success', 'ТС сохранено.');
//                    return $this->redirect('/vehicle/index');
                } else {

                    $model = $modelOLD;
                    $modelRegLicense = $modelRegLicenseOLD;
                    $model->save();
                    $modelRegLicense->save();
                    Yii::$app->session->setFlash('warning', 'Ошибка сохранения. Попробуйте позже.');
//                    return $this->redirect('/vehicle/index');
                }
                if(Yii::$app->user->can('admin'))return $this->redirect('/admin/vehicle/index');
                return $this->redirect($redirect);

            }
//                return var_dump($model->getErrors());
                Yii::$app->session->setFlash('warning', 'Ошибка сохранения. Попробуйте позже.');
            if(Yii::$app->user->can('admin'))return $this->redirect('/admin/vehicle/index');

            return $this->redirect('$redirect');
//            return 'OK';
            } else {

                return $this->render('update', [
                    'model' => $model,
                    'modelRegLicense' => $modelRegLicense,
                ]);
            }
        }

    /**
     * Deletes an existing Vehicle model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */

    public function actionSelectPricezones($id){
        return $id;
    }

    public function actionFullDelete($id){
        $model = $this->findModel($id);
        if($model) {
            $model->scenario = $model::SCENARIO_DELETE;
            $modelRegLic = RegLicense::find()->where(['id' => $model->reg_license_id])->one();
            $model->deleteFilePhoto();
            $model->status = $model::STATUS_FULL_DELETED;
            $model->save();
            if ($modelRegLic) {
                $modelRegLic->deletePhoto();
//                $modelRegLic->delete();
                Yii::$app->session->setFlash('success', 'ТС Удалено безвозвратно.');
                return $this->redirect(['index']);
            }
        }
        Yii::$app->session->setFlash('warning', 'Ошибка.');
        return $this->redirect(['index']);
    }

    //action для аякс запроса
    public function actionUpdatePricezones(){
        $post = Yii::$app->request->post();
        $id_vehicle = $post['id_vehicle'];
        $vehicle = Vehicle::findOne(['id' => $id_vehicle]);
        $veh_type = $vehicle->id_vehicle_type;
        $body_type = $post['body_type'];
//return $body_type;
        $result = [];
        $priceZones = PriceZone::find()->where(['status' => PriceZone::STATUS_ACTIVE]);
        switch ($veh_type) {
            case Vehicle::TYPE_TRUCK:
                $tonnage = $post['tonnage'];
                $length = $post['length'];
                $volume = $post['volume'];

                $longlength = $post['longlength'];
                $priceZones = $priceZones
                    ->andFilterWhere(['<=', 'tonnage_min', $tonnage])
                    ->andFilterWhere(['<=', 'volume_min', $volume])
                    ->andFilterWhere(['<=', 'length_min', $length])
                ;
                (!$longlength)
                ? $priceZones = $priceZones->andFilterWhere(['longlength' => $longlength])->orderBy(['r_km'=>SORT_DESC, 'r_h'=>SORT_DESC])->all()
                : $priceZones = $priceZones->orderBy(['r_km'=>SORT_DESC, 'r_h'=>SORT_DESC])->all();
                break;
            case Vehicle::TYPE_PASSENGER:
                $passengers = $post['passengers'];

                $priceZones = $priceZones
                    ->andFilterWhere(['>=', 'passengers', $passengers])
                    ->all();
                break;
            case Vehicle::TYPE_SPEC:
//                return $tonnage;
                $tonnage = $post['tonnage'];
                $length = $post['length'];
                $volume = $post['volume'];
                $tonnage_spec = $post['tonnage_spec'];
                $length_spec = $post['length_spec'];
                $volume_spec = $post['volume_spec'];
                switch ($body_type){
                    case Vehicle::BODY_manipulator:
                        $priceZones = $priceZones
                            ->andFilterWhere(['<=', 'tonnage_spec_min', $tonnage_spec])
                            ->andFilterWhere(['<=', 'length_spec_min', $length_spec])
                            ->andFilterWhere(['<=', 'tonnage_min', $tonnage])
                            ->andFilterWhere(['<=', 'length_min', $length])
                        ;
                        break;
                    case Vehicle::BODY_dump:
//                        return $tonnage;
                        $priceZones = $priceZones
                            ->andFilterWhere(['<=', 'tonnage_min', $tonnage])
                            ->andFilterWhere(['<=', 'volume_min', $volume])
                        ;
                        break;
                    case Vehicle::BODY_crane:
                        $priceZones = $priceZones
                            ->andFilterWhere(['<=', 'tonnage_spec_min', $tonnage_spec])
                            ->andFilterWhere(['<=', 'length_spec_min', $length_spec])
                        ;
                        break;
                    case Vehicle::BODY_excavator:
                        $priceZones = $priceZones
                            ->andFilterWhere(['<=', 'volume_spec', $volume_spec])
                        ;
                        break;
                    case Vehicle::BODY_excavator_loader:
                        $priceZones = $priceZones
                            ->andFilterWhere(['<=', 'volume_spec', $volume_spec])
                        ;
                        break;
                }
                $priceZones = $priceZones
                    ->orderBy(['r_km'=>SORT_DESC, 'r_h'=>SORT_DESC])
                    ->all()
                ;
                break;
        }
        foreach ($priceZones as $priceZone) {
            if ($priceZone->hasBodyType($body_type)) {
                $priceZone = $priceZone->getPriceZoneForCarOwner($vehicle->id_user);
                $result[] = [
                    'id' => $priceZone->id,
                    'name' => 'Тарифная зона ' . $priceZone->id,
                    'r_km' => $priceZone->r_km,
                    'r_h' => $priceZone->r_h,
                    'helpMes' => $priceZone->printHtml(),
                ];
            }
        }
        return json_encode($result);
    }

    public function actionDelete($id, $redirect = '/vehicle')
    {
        $model = $this->findModel($id);
        if($model->hasOrder(Order::STATUS_VEHICLE_ASSIGNED)){
            functions::setFlashWarning('У этого ТС есть незавершенные заказы! Нельзя удалить это ТС. Прежде завершите заказ.');
            return $this->redirect($redirect);
        }
        $model->scenario = $model::SCENARIO_DELETE;
        $model->status = $model::STATUS_DELETED;
        ($model->save())
            ?Yii::$app->session->setFlash('success', 'ТС не активно.')
            :Yii::$app->session->setFlash('warning', 'Ошибка удаления ТС. Попробуйте позже.')
        ;
//        return var_dump($model[errors]);
            $this->redirect([$redirect]);
    }

    /**
     * Finds the Vehicle model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Vehicle the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Vehicle::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionValidateVehicleForm(){
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

//            $model = new VehicleForm();
            $model = new RegLicense();
            if($model->load(Yii::$app->request->post()))
                return \yii\widgets\ActiveForm::validate($model);
        }
        throw new \yii\web\BadRequestHttpException('Bad request!');
    }

    public function actionValidateVehicle(){
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

//            $model = new VehicleForm();
            $model = new Vehicle();
            $model2 = new RegLicense();
            if($model->load(Yii::$app->request->post()))
                return \yii\widgets\ActiveForm::validate($model);
            if($model2->load(Yii::$app->request->post()))
                return \yii\widgets\ActiveForm::validate($model2);
        }
        throw new \yii\web\BadRequestHttpException('Bad request!');
    }


}
