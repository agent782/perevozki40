<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Vehicle */

$this->title = $vehicle->id;
\yii\web\YiiAsset::register($this);
?>
<div class="vehicle-view">

    <h1><?= Html::encode($this->title) ?></h1>



    <?= DetailView::widget([
        'model' => $vehicle,
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
