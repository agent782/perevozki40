<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Company */

$this->title = 'Редактирование юл. лица: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Companies', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';


?>

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'modelCompany' => $modelCompany,
        'XcompanyXprofile' => $XcompanyXprofile,
    ]) ?>


