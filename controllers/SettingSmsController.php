<?php

namespace app\controllers;

use Yii;
use app\models\settings\SettingSMS;
use app\models\settings\SettingSMSSearch;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SettingSmsController implements the CRUD actions for SettingSMS model.
 */
class SettingSmsController extends Controller
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
     * Lists all SettingSMS models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = SettingSMS::find()->one();
        if(!$model) throw new HttpException(404,'Ошибка БД');

        return $this->render('index', [
            'model' => $model
        ]);
    }
    /**
     * Finds the SettingSMS model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SettingSMS the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SettingSMS::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
