<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\RequestPayment */

$this->title = 'Create Request Payment';
$this->params['breadcrumbs'][] = ['label' => 'Request Payments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="request-payment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
