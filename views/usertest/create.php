<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Usertest */

$this->title = Yii::t('app', 'Create Usertest');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Usertests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="usertest-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
