<?php

namespace app\modules\admin\controllers;
use app\components\functions\functions;
use app\models\Passport;
use app\models\Profile;
use app\models\ProfileSearch;
use app\models\User;
use app\models\UserSearch;
use yii\web\Controller;
use yii\filters\AccessControl;use Yii;
use yii\data\SqlDataProvider;
use app\models\auth_item;

/**
 * Default controller for the `admin` module
 */
class UsersController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     *
     */

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'update', 'delete', 'check-users-updates', 'confirm-profile-update',
                            'cancelProfileUpdate'],
                        'allow' => true,
                        'roles' => ['admin', 'finance', 'dispetcher']
                    ],
                ]]];
    }



    public function actionIndex(){
        $modelUser = functions::findCurrentUser();

        $searchModel = new  UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', compact(['modelUser','dataProvider','searchModel']));
    }

    public function actionView($id)
    {
        $this->layout = functions::getLayout();
        $model = $this->findModel($id);
        $profile = $model->profile;
        return $this->render('view',
            array_merge([
            'model' => $model,
            'profile' => $profile,
        ], User::arrayBalanceParamsForRender($id)));
    }

    /**
     * Creates a new Test model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
//    public function actionCreate()
//    {
//        $model = new Test();
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return $this->render('create', [
//                'model' => $model,
//            ]);
//        }
//    }

    /**
     * Updates an existing Test model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $redirect)
    {
        $model = User::find()->where(['id' => $id])->one();
        $profile = $model->profile;

        if ($model->load(Yii::$app->request->post()) && $profile->load(Yii::$app->request->post())) {
            if ($model->save() && $profile->save()) {

                return $this->redirect([$redirect]);
            } else {
                return $this->render('update', [
                    'model' => $model, 'profile' => $profile]);
            }
        }
        else {
            return $this->render('update', [
                'model' => $model, 'profile' => $profile]);
        }
    }

    /**
     * Deletes an existing Test model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionCheckUsersUpdates($redirect = '/admin/users/check-users-updates'){
        $searchModel = new  ProfileSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        $dataProvider->query->andWhere(['in', 'check_update_status', [Profile::CHECK_UPDATE_STATUS_WAIT]]);

        return $this->render('check-users-updates', compact(['dataProvider','searchModel', 'redirect']));
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = User::STATUS_DELETED;
        $model->save();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Test model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Test the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionConfirmProfileUpdate($id_user, $redirect = '/admin/users/check-users-updates'){
        $Profile = Profile::findOne(['id_user' => $id_user]);
        if(!$Profile) {
            functions::setFlashWarning('Такого профиля не существует!');
            return $this->redirect($redirect);
        }
        if (!$Profile->update_to_check) {
            $Profile->check_update_status = $Profile::CHECK_UPDATE_STATUS_NO;
            $Profile->save(false);
            functions::setFlashWarning('Данные для редактирования не найдены!');

        } else {
            if ($Profile->photo != $Profile->update_to_check['photo'] && $Profile->update_to_check['photo']) {
                $photo_upd = Yii::getAlias('@userUpdatePhotoDir/') . $Profile->update_to_check['photo'];
                if (file_exists($photo_upd)) {
                    $filename = $Profile->id_user . '.' . pathinfo($photo_upd, PATHINFO_EXTENSION);
                    $photo_save = Yii::getAlias('@userPhotoDir/') . $filename;
                    if (rename($photo_upd, $photo_save)) {
                        functions::setFlashInfo('Фото изменено');
                        $Profile->photo = $filename;
                    } else {
                        functions::setFlashInfo('Фото не изменено');
                    }
                } else {
                    functions::setFlashInfo('Фото не изменено_1');
                }
            }
            if($Profile->update_to_check['passport_number']){
                if($Profile->passport){
                    $Profile->Update['passport'] = $Profile->passport->attributes();
                    if($Profile->passport->number != $Profile->update_to_check['passport_number']
                        || $Profile->passport->date != $Profile->update_to_check['passport_date']
                        || $Profile->passport->place != $Profile->update_to_check['passport_place']
                        || $Profile->passport->country != $Profile->update_to_check['country']
                    ){
                        $Profile->passport->number = $Profile->update_to_check['passport_number'];
                        $Profile->passport->date = $Profile->update_to_check['passport_date'];
                        $Profile->passport->place = $Profile->update_to_check['passport_place'];
                        $Profile->passport->country = $Profile->update_to_check['country'];

                        if(!$Profile->passport->save(false)){
                            $Profile->Update['passport'] = [];
                            functions::setFlashWarning('Ошибка сохранения данных паспорта!');
                        }
                    }
                } else {
                    $Passport = new Passport();
                    $Passport->number = $Profile->update_to_check['passport_number'];
                    $Passport->date = $Profile->update_to_check['passport_date'];
                    $Passport->place = $Profile->update_to_check['passport_place'];
                    $Passport->country = $Profile->update_to_check['country'];

                    if($Passport->save(false)){
                        $Profile->id_passport = $Passport->id;
                    } else {
                        functions::setFlashWarning('Ошибка сохранения данных паспорта!');
                    }
                }
            }
            if($Profile->update_to_check['email'] != $Profile->email){
                $Profile->Update['user'] = $Profile->user->attributes;
                $Profile->user->email = $Profile->update_to_check['email'];
                if(!$Profile->user->save(false)){
                    $Profile->Update['user'] = [];
                    functions::setFlashWarning('шибка сохранения email');
                }
            }
            $update_attrs = $Profile->update_to_check;
            if (array_key_exists('photo', $Profile->update_to_check)) {
                unset($update_attrs['photo']);
            }
            $Profile->Update['profile'] = $Profile->attributes;
            $Profile->setAttributes($update_attrs, false);

            $Profile->check_update_status = $Profile::CHECK_UPDATE_STATUS_YES;
            $Profile->update_to_check = null;
            $history_updates = $Profile->history_updates;
            if(!$history_updates) $history_updates = [];
            $history_updates[] = [
                time() => $Profile->Update
            ];
            $Profile->history_updates = $history_updates;
            if ($Profile->save()) {
                functions::setFlashSuccess('Профиль успешно изменен');
            } else {
                $Profile->Update['profile'] = [];
                functions::setFlashWarning('Ошибка на сервере. Профиль не ихменен.');
            }
        }
        return $this->redirect($redirect);
    }

    public function actionCancelProfileUpdate($id_user, $redirect = '/admin/users/check-users-updates'){
        $Profile = Profile::findOne(['id_user' => $id_user]);
        if(!$Profile){
            functions::setFlashWarning('Такого профиля не существует!');
        } else {
            if (!$Profile->update_to_check) {
                $Profile->check_update_status = $Profile::CHECK_UPDATE_STATUS_NO;
                $Profile->save(false);
                functions::setFlashWarning('Данные для редактирования не найдены!');
            } else {
                $Profile->update_to_check = '';
                $Profile->check_update_status = $Profile::CHECK_UPDATE_STATUS_NO;
                $Profile->save(false);
                functions::setFlashInfo('Изменение данных пользователя отменены');
            }
        }

        return $this->redirect($redirect);
    }
}
