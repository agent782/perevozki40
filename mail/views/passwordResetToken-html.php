<?php

use yii\helpers\Html;

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['/default/reset-password', 'token' => $user->password_reset_token]);
?>

<div class="password-reset">
    <p>Добрый день, <?= Html::encode($user->profile->name) ?>!</p>
    <p>Для изменения пароля пройдите по ссылке:</p>
    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
</div>