<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\MaskedInput;

$this->title = 'Вход в личный кабинет';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
<div class="site-login">
    <h4><?= Html::encode($this->title) ?></h4>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>

        <?= $form->field($model, 'username')
            ->textInput(['autofocus' => true])
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

            ])
        ?>

        <?= $form->field($model, 'password')->passwordInput()?>

        <?= $form->field($model, 'rememberMe')->checkbox([
            'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{label} {input}</div>\n<div class=\"col-lg-8\">{error}</div>",
        ])?>

        <div class="form-group">
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::submitButton('Войти', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>
        <p><a href="/default/signup"><span style="font-size: 18px"> Зарегистрироваться</span></a></p>
        <a href="">Восстановить пароль</a>
    </div>


</div>
