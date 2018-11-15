<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 10.01.2018
 * Time: 16:28
 */
?>
<?= Yii::$app->user->getId()?>
<br>
<?= Yii::$app->user->identity->username?>
<br>
<?= Yii::$app->user->identity->email?>
<br>
<?= Yii::$app->user->identity->dateCreatedAt?>
<br>
<?= Yii::$app->user->identity->profile->name?>
<br>
<?= Yii::$app->user->identity->profile->surname?>
<br>
<?= Yii::$app->user->identity->profile->patrinimic?>
<br>
<?= Yii::$app->user->identity->profile->getSex()?>
