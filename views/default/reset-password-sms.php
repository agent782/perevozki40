<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 11.07.2019
 * Time: 14:15
 */
/* @var $model ResetPasswordSmsForm
 *
 */
use app\models\ResetPasswordSmsForm;
use yii\bootstrap\ActiveForm;
use yii\widgets\MaskedInput;
use yii\captcha\Captcha;
use yii\bootstrap\Html;
use yii\helpers\Url;
?>

<div class="container">
    <?php
        $form = ActiveForm::begin();
    ?>
    <?= $form->field($model, 'phone')
        ->textInput(['autofocus' => true])
        ->widget(MaskedInput::className(),[
            'mask' => '+7(999)999-99-99',
            'clientOptions'=>[
                'removeMaskOnSubmit' => true,
            ],
            'options' => [
                'type' => 'tel',
            ]
        ])
    ?>
    <?= $form->field($model, 'captcha')->widget(Captcha::class, [
//        'captchaAction' => 'default/captcha'
    ])?>
    <?= Html::submitButton('Отправить код по СМС', ['class' => 'btn btn-info', 'name' => 'button', 'value' => 'send_sms'])?>
    <br><br>
    <?= Html::submitButton('У меня есть код из СМС', [
        'class'=>'btn btn-primary',
        'name' => 'button',
        'value' => 'change-password'
    ])?>
    <?php
        $form::end();
    ?>
</div>
