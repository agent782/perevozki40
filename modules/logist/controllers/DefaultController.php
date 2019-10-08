<?php

namespace app\modules\logist\controllers;

use app\models\Order;
use yii\web\Controller;

/**
 * Default controller for the `logist` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $model = 1;
        return $model->id;

        return $this->render('index');
    }
}
