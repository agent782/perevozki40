<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 14.06.2019
 * Time: 11:14
 */
/* @var $User \app\models\User
 * @var $VerifyPhone \app\models\VerifyPhone
 */


use kartik\form\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\MaskedInput;
?>

<?php
    $form = ActiveForm::begin([
         'enableAjaxValidation' => true,
        'validationUrl' => \yii\helpers\Url::to(['/default/validate-phone']), // Добавить URL валидации
    ]);
?>
<?= $form->field($User, 'username')
    ->textInput(['autofocus' => true])
    ->widget(MaskedInput::className(),[
        'mask' => '+7(999)999-99-99',
        'clientOptions'=>[
            'removeMaskOnSubmit' => true,
        ],
        'options' => [
            'type' => 'tel',
            'autocorrect' => 'off',
            'autocomplete' => 'tel',
        ]
    ])?>
<?= $form->field($User, 'captcha', [
    'enableAjaxValidation' => false,
    'enableClientValidation' => true
])->widget(\yii\captcha\Captcha::className(), ['options' => ['style' => 'width: 100px;']])?>

<?= Html::a('Получить смс-код для подтверждения', ['/user/change-phone', 'id_user' => $User->id], ['id' => 'send-sms'])?>
<?php
\yii\widgets\Pjax::begin(['id' => 'sms-container']);
?>

<?php
    \yii\widgets\Pjax::end();
?>
<?php
    $form::end();
?>

<script>
    $('#send-sms').on('click', function () {
        alert();
        $.pjax.reload({
            url : "/user/change-phone?id_user=147",
            container: "#sms-container",
            dataType:"json",
            type: "POST",
            data: {
                "id_user" : 147
            }
        })
    })
</script>