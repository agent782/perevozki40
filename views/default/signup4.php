<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 08.01.2018
 * Time: 16:13
 */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
$this->title = 'Регистрация (4/4)';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h4><?= Html::encode($this->title) ?></h4>
<!--    <p>Please fill out the following fields to signup:</p>-->
    <div class="row">
        <div class="col-lg-5">

            <?php $form = ActiveForm::begin(['id' => 'form-signup',
                'enableAjaxValidation' => true,
                'validationUrl' => \yii\helpers\Url::to(['validate-email']), // Добавить URL валидации]);
            ])?>
            <?= $form->field($modelSignupUserForm, 'email')->input('email') ?>
            <?= $form->field($modelSignupUserForm, 'password')->passwordInput() ?>

            <div class="form-group">
                <?= $form->field($modelSignupUserForm, 'confidentiality_agreement')
                    ->checkbox()->label('Я ознакомлен и согласен с '
                        . Html::a('"Соглашением о конфиденциальности".', '/default/policy'))?>
                <?= $form->field($modelSignupUserForm, 'use_conditions')->checkbox()
                    ->label('Я ознакомлен и согласен с '
                        . Html::a('"Условиями использования сервиса perevozki40.ru".', '/default/user-agreement'))?>
                <?= Html::submitButton('Завершить регистрацию', ['class' => 'btn btn-primary', 'name' => 'button', 'value' => 'signup4']) ?>
            </div>
            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>


