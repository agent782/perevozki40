<?php

namespace app\modules\finance\controllers;

use app\models\Report;
use yii\base\DynamicModel;
use yii\filters\AccessControl;

class ReportsController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ]
                ]
            ]
        ];
    }

    public function actionIndex()
    {
        $model = new Report();

        $model->date1 = date('d.m.Y', time());
        $model->date2 = $model->date1;

        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            return var_dump($model->getReportOrders());
        }

        return $this->render('index',[
            'model' => $model
        ]);
    }

}
