<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 08.01.2018
 * Time: 14:32
 */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
$this->title = 'Подтверждение номера телефона';
?>
<div>
    <?= Yii::$app->session->getFlash('errorCode')?>
</div>
<?php
$form = ActiveForm::begin();
?>
<?= $form->field($modelVerifyPhone, 'userCode', ['inputOptions' => [
    'type' => 'tel',
    'autocorrect' => 'off',
    'autocomplete' => 'off'
]])

?>
<?= Html::submitButton('Подтвердить', [
    'class' => 'btn btn-primary',
    'name' => 'button',
    'value' => 'signup3'
])?>
<?php
ActiveForm::end();
?>
<br>
<?= $modelUser->username ?>
<br>
<?=$modelProfile->name?>
<br>


<?= $modelVerifyPhone->getVerifyCode() ?>
<?= $modelVerifyPhone->userCode ?>
<?php
   echo (Yii::$app->session->getFlash('modelVerifyPhone'));
?>
<?= $modelVerifyPhone->checkUserCode() ?>
<?= $modelProfile->sex?>
