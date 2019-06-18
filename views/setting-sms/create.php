<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\settings\SettingSMS */

$this->title = 'Create Setting Sms';
$this->params['breadcrumbs'][] = ['label' => 'Setting Sms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="setting-sms-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
