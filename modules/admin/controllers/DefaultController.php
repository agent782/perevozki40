<?php

namespace app\modules\admin\controllers;
use app\components\functions\functions;
use app\models\Order;
use app\models\User;
use app\models\UserSearch;
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
        Yii::$app->db->createCommand()->truncateTable('price_zone')->execute();
        Yii::$app->db->createCommand()->truncateTable('XorderXrate')->execute();

        functions::setFlashSuccess('Прайс-лист очищен');
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
                if($user->profile) {
                    $user->profile->unlinkAll('companies', true);
//                    Yii::$app->db->createCommand()->truncateTable('passports')->execute();
                    if($user->profile->passport){
                        $user->profile->passport->delete();
                    }
                    if($user->profile->driverLicense){
                        $user->profile->driverLicense->delete();
                    }
                    if($user->drivers){
                        foreach ($user->drivers as $driver) {
                            if($driver->passport) {
                                $driver->passport->delete();
                            }
                            if($driver->license) {
                                $driver->license->delete();
                            }
                            $driver->delete();
                        }
                    }

                    if($user->vehicles){
                        foreach ($user->vehicles as $vehicle){
                            $vehicle->unlinkAll('loadingtypes', true);
                            $vehicle->unlinkAll('price_zones', true);
                            if($vehicle->regLicense){
                                $vehicle->regLicense->delete();
                            }

                            $vehicle->delete();
                        }
                    }

//                    $user->profile->delete();
                }
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
