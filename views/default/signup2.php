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
$this->title = 'Регистрация';
?>

    <h3><?=$this->title?></h3>

<?php
$form = ActiveForm::begin([
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
        'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
    'enableAjaxValidation' => true,
    'validationUrl' => \yii\helpers\Url::to(['validate-phone']), // Добавить URL валидации
]);
?>
<?= $form->field( $modelUser, 'username')
    ->textInput(['autofocus' => 'autoFocus'])
    ->widget(MaskedInput::className(),[
        'mask' => '+7(999)999-99-99',
        'clientOptions'=>[
            'removeMaskOnSubmit' => true,
        ],
        'options' => [
            'type' => 'tel',
            'autocorrect' => 'off',
            'autocomplete' => 'tel'
        ]
    ])?>
<?= Html::submitButton('Подтвердить', [
    'class' => 'btn btn-primary',
    'name' => 'button',
    'value' => 'signup2'
])?>
<?php
ActiveForm::end();
?>
<?=$modelProfile->name?>
