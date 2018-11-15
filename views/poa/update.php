<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\XprofileXcompany */

$this->title = 'Update Xprofile Xcompany: ' . $model->id_profile;
$this->params['breadcrumbs'][] = ['label' => 'Xprofile Xcompanies', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_profile, 'url' => ['view', 'id_profile' => $model->id_profile, 'id_company' => $model->id_company]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="xprofile-xcompany-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
