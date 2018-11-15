<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\XprofileXcompany */

$this->title = 'Create Xprofile Xcompany';
$this->params['breadcrumbs'][] = ['label' => 'Xprofile Xcompanies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="xprofile-xcompany-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
