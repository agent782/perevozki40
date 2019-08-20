<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Driver */

$this->title = 'Новый водитель';
$this->params['breadcrumbs'][] = ['label' => 'Водители', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container driver-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
        'DriverForm' => $DriverForm,
//        'modelPassport' => $modelPassport,
//        'modelDriverLicense' => $modelDriverLicense,
    ]) ?>

</div>
