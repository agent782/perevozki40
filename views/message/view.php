<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Message */

\yii\web\YiiAsset::register($this);
 ?>
<div class="message-view">
    <?= $model->text?>


</div>
