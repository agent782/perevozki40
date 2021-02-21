<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 08.01.2018
 * Time: 12:50
 */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\widgets\MaskedInput;
$this->title = 'Регистрация (2/4)';
//var_dump($_SESSION['modelProfile']->name);
?>


<div>
    <h4><?=$this->title?></h4>
<?php
$form = ActiveForm::begin([
    'fieldConfig' => [
//        'template' => '{label}<div class="col-lg-3">{input}</div><div class="col-lg-8">{error}</div>',
//        'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
    'enableAjaxValidation' => true,
//    'enableClientValidation' => false,
    'validationUrl' => \yii\helpers\Url::to(['validate-phone']), // Добавить URL валидации
]);
?>
    <?= $form->field($modelProfile, 'name')->hiddenInput()->label(false)?>
<?= $form->field( $modelUser, 'username', ['errorOptions' => ['class' => 'help-block' ,'encode' => false]])
    ->textInput(['autofocus' => true])
    ->widget(MaskedInput::className(),[
        'mask' => '+7(999)999-99-99',
        'clientOptions'=>[
            'removeMaskOnSubmit' => true,
        ],
        'options' => [
            'type' => 'tel',
//            'autocorrect' => 'off',
//            'autocomplete' => 'tel',
        ]
    ])?>

<?= $form->field($modelUser, 'captcha', [
    'enableAjaxValidation' => false,
    'enableClientValidation' => true
])->widget(\yii\captcha\Captcha::className(), ['options' => ['style' => 'width: 100px;']])?>

<?= Html::submitButton('Получить смс-код', [
    'class' => 'btn btn-primary',
    'name' => 'button',
    'value' => 'signup2'
])?>
<?php
ActiveForm::end();
?>
</div>
