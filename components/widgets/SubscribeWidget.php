<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 01.12.2020
 * Time: 9:05
 */

namespace app\components\widgets;


use app\models\Subscribe;
use kartik\form\ActiveForm;
use yii\base\Widget;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;

class SubscribeWidget extends Widget
{
    public function run()
    {
        self::showForm();
    }

    public function showForm(){

        Modal::begin([
            'toggleButton' => [
                'label' => 'Нажмите, чтобы подписаться на наши <br>новости и спецпредложения',
                'class' => 'btn btn-lg btn-primary',
                'style' => [
//                    'width' => ''
                ]
            ]
        ]);

        $SubscribeModel = new Subscribe();

        $form = ActiveForm::begin([
            'enableAjaxValidation' => true,
            'validationUrl' => \yii\helpers\Url::to(['validate-subscribe']),
        ]);
        echo $form->field($SubscribeModel, 'email')->input('email', [
            'placeholder' => 'Введите Ваш email'
        ])->label(false);
        echo Html::submitButton('Подписаться', ['class' => 'btn btn-info']);
?>
        <comment>
            * В каждом письме будет ссылка, если Вы захотите отписаться.
        </comment>
<?php

        ActiveForm::end();


        Modal::end();

    }


}