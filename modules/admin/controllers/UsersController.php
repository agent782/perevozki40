<?php

namespace app\modules\admin\controllers;
use app\components\functions\functions;
use app\models\Profile;
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
                        'allow' => true,
                        'roles' => ['admin']
                    ]]]];
    }



    public function actionIndex(){
        $modelUser = functions::findCurrentUser();

        $searchModel = new  UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', compact(['modelUser','dataProvider','searchModel']));
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $profile = $model->profile;
        return $this->render('view', [
            'model' => $model,'profile' => $profile,
        ]);
    }

    /**
     * Creates a new Test model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Test();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Test model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $profile = $model->profile;

        if ($model->load(Yii::$app->request->post()) || $profile->load(Yii::$app->request->post())) {
            if ($model->save() && $profile->save()) {

                return $this->redirect(['index']);
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
}
