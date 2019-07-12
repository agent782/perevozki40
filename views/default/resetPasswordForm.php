<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Изменение пароля';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-reset-password">
    <h3><?= Html::encode($this->title) ?></h3>
    <p>Пожалуйста, введите новый пароль:</p>
    <div class="row">
        <div class="col-lg-5">

            <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>
            <?= $form->field($model, 'password')->passwordInput(['autofocus' => true])->label('Новый пароль') ?>
            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>

        </div>
    </div>
    Для безопасности Ваших данных сохраните пароль в надежном месте и никому его не сообщайте.

</div>