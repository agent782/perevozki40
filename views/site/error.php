<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;
$this->title = $name;
?>
<div class="site-error">


    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <a href="/" type="button" class="btn btn-primary">На гравную</a>
    <a href="/user" type="button" class="btn btn-primary">Личный кабинет</a>
</div>
