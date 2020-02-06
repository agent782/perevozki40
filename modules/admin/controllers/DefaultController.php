<?php

namespace app\modules\admin\controllers;
use app\components\functions\functions;
use app\models\Message;
use app\models\Order;
use app\models\Route;
use app\models\User;
use app\models\UserSearch;
use function GuzzleHttp\Promise\all;
use yii\web\Controller;
use yii\filters\AccessControl;use Yii;
use yii\data\SqlDataProvider;
use app\models\auth_item;
use yii\web\HttpException;

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
//        $order = Order::findOne(288);
//        return var_dump($order);
//        return var_dump($order->getSuitableVehicles());

//        return var_dump($order->getSortArrayCarOwnerIdsForFind());

        foreach (Route::find()-all() as $route){
            $route->routeStart = str_replace('Россия, ', '', $route->routeStart);
            $route->route1 = str_replace('Россия, ', '', $route->route1);
            $route->route2 = str_replace('Россия, ', '', $route->route2);
            $route->route3 = str_replace('Россия, ', '', $route->route3);
            $route->route4 = str_replace('Россия, ', '', $route->route4);
            $route->route5 = str_replace('Россия, ', '', $route->route5);
            $route->route6 = str_replace('Россия, ', '', $route->route6);
            $route->route7 = str_replace('Россия, ', '', $route->route7);
            $route->route8 = str_replace('Россия, ', '', $route->route8);
            $route->routeFinish = str_replace('Россия, ', '', $route->routeFinish);
            $route->save(false);

        }

        echo $route->routeStart . '<br>';
        echo str_replace('Россия, ', '', $route->routeStart);
//        return;
        return $this->render('index');
    }

    public function actionSystemTools(){
        return $this->render('system-tools');
    }

    public function actionDeleteAllOrders(){
        if(!Yii::$app->user->can('admin')){
            throw new HttpException(403, 'Вы не админ!!!');
        }
        Yii::$app->db->createCommand()->truncateTable('XorderXloadingtype')->execute();
        Yii::$app->db->createCommand()->truncateTable('XorderXrate')->execute();
        Yii::$app->db->createCommand()->truncateTable('XorderXtypebody')->execute();
        Yii::$app->db->createCommand()->truncateTable('routes')->execute();
        Yii::$app->db->createCommand()->truncateTable('Orders')->execute();

        functions::setFlashSuccess('Журнал заказов очищен');

        return $this->redirect('/admin/default/system-tools');
    }

    public function actionDeleteAllPrice(){
        if(!Yii::$app->user->can('admin')){
            throw new HttpException(403, 'Вы не админ!!!');
        }
//        Yii::$app->db->createCommand()->truncateTable('price_zone')->execute();
//        Yii::$app->db->createCommand()->truncateTable('XorderXrate')->execute();
//
//        functions::setFlashSuccess('Прайс-лист очищен');
        functions::setFlashWarning('Операция не доступна!');
        return $this->redirect('/admin/default/system-tools');
    }

    public function actionDeleteAllUsers(){
        if(!Yii::$app->user->can('admin')){
            throw new HttpException(403, 'Вы не админ!!!');
        }

        $Users = User::find()
            ->where(['>', 'id', 10])
            ->all();
        foreach ($Users as $user){
            if($user) {
                if($user->delete()) {
                    functions::setFlashSuccess('OK');
                } else{
                    functions::setFlashWarning('id ' . $user->id . ' не удален');
//                    break;
                }
            }
        }
        return $this->redirect('/admin/default/system-tools');

        return User::find()->count();

    }

}
