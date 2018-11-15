<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\PriceZone */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Price Zones', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="price-zone-view">
    <h2>Тарифная зона <?=$model->id?></h2>
    <?=$model->printHtml();?>

</div>
