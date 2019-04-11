<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Vehicle */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Vehicles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="vehicle-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'id_user',
            'id_vehicle_type',
            'body_type',
            'tonnage',
            'length',
            'width',
            'height',
            'volume',
            'longlength',
            'passengers',
            'ep',
            'rp',
            'lp',
            'tonnage_spec',
            'length_spec',
            'volume_spec',
            'description:ntext',
            'create_at',
            'update_at',
            'status',
            'rating',
            'reg_license_id',
            'photo:ntext',
            'error_mes:ntext',
        ],
    ]) ?>

</div>
