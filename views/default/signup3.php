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
]])->input('text')
?>

    <?= Yii::$app->session->getFlash('errorCode')?>
<br><br>
<?= Html::submitButton('Подтвердить', [
    'class' => 'btn btn-primary',
    'name' => 'button',
    'value' => 'signup3'
])?>
<?php
ActiveForm::end();
?>

<?= $modelUser->username ?>
<br>
<?=$modelProfile->name?>
<br>
<input type="number" id="hidden" hidden value="<?=$modelVerifyPhone->getVerifyCode()?>">

КОД<?= var_dump($modelVerifyPhone->getVerifyCode())?><br>
КОД2<?= $modelVerifyPhone->userCode ?><br>
КОД<?= var_dump($modelVerifyPhone) ?><br>
<?php
   echo (Yii::$app->session->getFlash('modelVerifyPhone'));
?>
<?= $modelVerifyPhone->checkUserCode() ?>
<?= $modelProfile->sex?>
<!--<script>-->
<!--    $('#UserCode').change(function () {-->
<!--        var UCode = $('#UserCode').val();-->
<!--        var VCode = $('#hidden').val();-->
<!--        alert(VCode);-->
<!--        $.ajax({-->
<!--            type:'POST',-->
<!--            url:'/default/check-code',-->
<!--            dataType: 'JSON',-->
<!--            data:{-->
<!--                Ucode:UCode-->
<!--            },-->
<!--            success:function () {-->
<!--                alert('OK');-->
<!--            },-->
<!--            error:function () {-->
<!--                alert('ER');-->
<!--            }-->
<!--        });-->
<!--    })-->
<!--</script>-->