<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Company */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Companies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="company-view">

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
            'inn',
            'name',
            'address',
            'address_real',
            'address_post',
            'value',
            'address_value',
            'branch_type',
            'capital',
            'email:email',
            'email2:email',
            'email3:email',
            'kpp',
            'management_name',
            'management_post',
            'name_full',
            'name_short',
            'ogrn',
            'ogrn_date',
            'okpo',
            'okved',
            'opf_short',
            'phone',
            'phone2',
            'phone3',
            'citizenship',
            'state_actuality_date',
            'state_registration_date',
            'state_liquidation_date',
            'state_status',
            'data_type',
            'status',
            'raiting',
            'created_at',
            'updated_at',
            'FIO_contract',
            'basis_contract',
            'job_contract',
        ],
    ]) ?>

</div>
