<?php

namespace app\modules\admin\controllers;
use app\components\functions\functions;
use app\models\User;
use app\models\UserSearch;
use yii\web\Controller;
use yii\filters\AccessControl;use Yii;
use yii\data\SqlDataProvider;
use app\models\auth_item;

/**
 * Default controller for the `admin` module
 */
class DefaultController extends Controller
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

    public function actionIndex()
    {
        return $this->render('index');
    }



}
