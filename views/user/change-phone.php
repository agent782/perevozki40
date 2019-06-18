<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 14.06.2019
 * Time: 11:14
 */
/* @var $User \app\models\User
 * @var $VerifyPhone \app\models\VerifyPhone
 * @var $this \yii\web\View
 */


use kartik\form\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\MaskedInput;
?>
<?php
$this->registerJsFile('/js/update-phone.js');
$this->title = 'Изменение основного номера телефона';
?>
<?php
\yii\widgets\Pjax::begin([
        'id' => 'sms-code',
//        'enablePushState' => false,
//        'enableReplaceState' => false,
    ]);
//$this->registerJsFile('/js/update-phone.js');

?>
<div class="container">
    <h3><?= $this->title?></h3>
    <comment>Текущий номер телефона: <?=\app\models\User::findOne(Yii::$app->user->id)->username ?> </comment>
<?php
    $form = ActiveForm::begin([
        'id' => 'form-change-phone',
         'enableAjaxValidation' => true,
        'validationUrl' => \yii\helpers\Url::to(['/default/validate-phone']), // Добавить URL валидации
    ]);
?>
    <?= $form->field($User, 'new_username')->hiddenInput()->label(false)?>

    <?= $form->field($User, 'username', [
        //        'enableAjaxValidation' => false,
    'enableClientValidation' => true
    ])
    ->textInput(['autofocus' => true])
    ->widget(MaskedInput::className(),[
        'mask' => '+7(999)999-99-99',
        'clientOptions'=>[
            'removeMaskOnSubmit' => true,
        ],
        'options' => [
            'type' => 'tel',
            'id' => 'username',
            'autocorrect' => 'off',
            'autocomplete' => 'on',
        ]
    ])->label('Введите новый номер')?>
<?= Html::button('Получить смс-код для подтверждения', [
    'id' => 'send-sms',
    'class' => 'btn btn-info',
    'disabled' => ($timer)?true:false
])?>
<div id="time_mes" <?=($timer<=0)?'hidden':'';?> >
    Повторный код может быть выслан через <b id="timer"> <?= $timer ?> </b> секунд
</div>
<br><br>
<div>

    <?= $form->field($VerifyPhone, 'userCode', [
//        'enableAjaxValidation' => false,
        'enableClientValidation' => true
    ])->input('tel', ['autocomplete' => 'off'])?>

    <?=Html::submitButton('Изменить номер телефона', [ 'class' => 'btn btn-success'])?>
<?php
    $form::end();
?>
    <?php
    \yii\widgets\Pjax::end();
    ?>

</div>
</div>

