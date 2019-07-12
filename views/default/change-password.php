<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 11.07.2019
 * Time: 15:40
 */
/*
* @var $model ResetPasswordSmsForm
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
        $form=ActiveForm::begin([
//            'enableAjaxValidation' => true,
//            'validationUrl' => '/default/reset-password-form'
        ]);
    ?>
    <?= $form->field($model,'phone')->hiddenInput()->label(false)?>
    <?= $form->field($model, 'sms_code')?>
    <?= $form->field($model, 'password')->passwordInput()?>
    <?= $form->field($model, 'repeat_password')->passwordInput()?>
    <?= Html::submitButton('Изменить пароль', ['class' => 'btn btn-info', 'name' => 'button', 'value' => 'confirm-password'])?>

    <?php
        $form::end();
    ?>
</div>