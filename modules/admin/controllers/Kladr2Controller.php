<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 22.08.2017
 * Time: 14:50
 */

namespace app\modules\admin\controllers;


use yii\base\Model;
use yii\web\Controller;
use yii\widgets\ActiveForm;
use andkon\yii2kladr\Kladr;

class Kladr2Controller extends Controller
{
    public function actionIndex()
    {
        $address = new Model(); // your address model

        $form = ActiveForm::begin();

        echo $form->field($address, 'city_id')

            ->widget(Kladr::className(), [
                'type'    => Kladr::TYPE_CITY,
                'options' => [
                    //'placeHolder' => $model->getAttributeLabel('city_id'),
                    'class' => 'form__input'
                ]
            ])
            ->label(false);
    }
}