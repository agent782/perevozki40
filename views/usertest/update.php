<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Usertest */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Usertest',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Usertests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="usertest-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
