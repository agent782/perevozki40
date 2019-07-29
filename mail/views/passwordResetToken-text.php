<?php
use \yii\bootstrap\Html;
$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['/default/reset-password', 'token' => $user->password_reset_token]);
?>

    Добрый день, <?= Html::encode($user->profile->name) ?>!
    Для изменения пароля пройдите по ссылке:

<?= $resetLink ?>