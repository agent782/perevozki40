<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\settings\SettingSMS */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Setting Sms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="setting-sms-view">

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
            'last_num_contract',
            'noPhotoPath',
            'FLAG_EXPIRED_ORDER',
            'user_discount_cash',
            'client_discount_cash',
            'vip_client_discount_cash',
            'user_discount_card',
            'client_discount_card',
            'vip_client_discount_card',
            'procent_vehicle',
            'procent_vip_vehicle',
            'sms_code_update_phone',
        ],
    ]) ?>

</div>
