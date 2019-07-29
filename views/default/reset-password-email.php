<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Запрос на изменение пароля';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-request-password-reset">
    <h3><?= Html::encode($this->title) ?></h3>
    <p>Пожалуйста, введите Ваш Email. Вам придет письмо с ссылкой на изменение пароля.</p>
    <div class="row">
        <div class="col-lg-5">

            <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>
            <?= $form->field($model, 'email')->textInput(['autofocus' => true])->label('Ваш email') ?>
            <div class="form-group">
                <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>