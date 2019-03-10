<?php

use yii\bootstrap\Html;
use app\components\functions\functions;

/* @var $this yii\web\View */
/* @var $model app\models\Tip */

$this->title = 'Create Tip';
$this->params['breadcrumbs'][] = ['label' => 'Tips', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tip-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
