<?php
use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $content string main view render result */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>" />
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    <?= $content ?>
    <br>
    Инструкция для подключения PUSH уведомлений.
    <br>
    https://youtu.be/ggzdPpIRpdI
    <br><br>
<<<<<<< HEAD
    С Уважением, Сервис Региональных Перевозок <?= Html::a('perevozki40.ru', 'http://perevozki40.ru')?>
=======
    С Уважением, Сервис Региональных Перевозок <?= Html::a('perevozki40.ru', 'https://perevozki40.ru')?>
>>>>>>> 5855c47a5aef123b0f69ff6c061572432ecf1684
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
