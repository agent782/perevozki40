<?php
/* КOНТРОЛЛЕР ДОВЕРЕННОСТЕЙ POWER_OF_ATTORNEY (XprofileXcompany)*/
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 03.04.2018
 * Time: 13:42
 */

namespace app\controllers;


use app\models\XprofileXcompany;
use yii\web\Controller;

class PoaController extends Controller
{
    public function actionDownloadForm($id_company, $id_profile, $return){
        $modelPOA = XprofileXcompany::find()
            ->where(['id_profile' => $id_profile, 'id_company' => $id_company])
            ->one()
            ;
        if(!$modelPOA) \Yii::$app->session->setFlash('errorCreatePOA', 'Ошибка! Попробуйте еще раз или обратитесь к администратору.');

        if($modelPOA = $modelPOA->createPowerOfAttorneyForm()){
            if($modelPOA->save())
                return \Yii::$app->response->sendFile(\Yii::getAlias('@poa_forms/'.$modelPOA->url_form));
            else \Yii::$app->session->setFlash('errorCreatePOA', 'Ошибка! Попробуйте еще раз или обратитесь к администратору.');
        }
        else $this->redirect($return);
    }
}