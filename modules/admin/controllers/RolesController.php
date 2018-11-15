<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 19.12.2017
 * Time: 12:47
 */

namespace app\modules\admin\controllers;



use yii\web\Controller;
use yii\data\SqlDataProvider;
use Yii;
use app\models\auth_item;

class RolesController extends Controller
{


    public function actionIndex()
    {
        //RBAC
        $auth = \Yii::$app->authManager;
        //DataProvider для GridView
        $count = Yii::$app->db->createCommand(
            'SELECT COUNT(*) FROM auth_item WHERE type=1')
            ->queryScalar();

        $dataProvider = new SqlDataProvider([
            'sql' => 'SELECT * FROM auth_item WHERE type=1',
            'totalCount' => $count,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'created_at',
                    'name',
                ],
            ],
        ]);

        $model_auth_item = new auth_item();

        if($model_auth_item->load(Yii::$app->request->post())) {
            if ($model_auth_item->AddRole($model_auth_item->name)) {
                Yii::$app->session->setFlash('access','SAVE OK');
                return $this->refresh();
            } else {
                Yii::$app->session->setFlash('error','NOT SAVE');
            }
        }

        return $this->render('index', compact(['auth', 'dataProvider', 'model_auth_item']));
    }
}
