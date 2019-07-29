<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 08.01.2018
 * Time: 14:32
 */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'Подтверждение номера телефона (3/4)';

?>
<div>

<h4><?= $this->title?></h4>
<?php
$form = ActiveForm::begin([
//    'enableAjaxValidation' => true,
//    'validationUrl' => \yii\helpers\Url::to('validate-verify-phone')
]);
?>
<?= $form->field($modelVerifyPhone, 'userCode', ['inputOptions' => [
    'id' => 'UserCode',
    'type' => 'tel',
    'style' => 'width: 100px',
    'autocorrect' => 'off',
    'autocomplete' => 'off',
//    'onchange' => 'alert();'
]])->input('tel')
?>

    <?= Yii::$app->session->getFlash('errorCode')?>

<?= Html::submitButton('Подтвердить', [
    'class' => 'btn btn-primary',
    'name' => 'button',
    'value' => 'signup3'
])?>

<?php
ActiveForm::end();
?>
</div>

КОД<?= var_dump($modelVerifyPhone->getVerifyCode())?><br>
