<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\XprofileXcompany */

$this->title = $model->id_profile;
$this->params['breadcrumbs'][] = ['label' => 'Xprofile Xcompanies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="xprofile-xcompany-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id_profile' => $model->id_profile, 'id_company' => $model->id_company], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id_profile' => $model->id_profile, 'id_company' => $model->id_company], [
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
            'id_profile',
            'id_company',
            'job_post',
            'url_form:ntext',
            'url_upload_poa:ntext',
            'url_poa:ntext',
            'term_of_office',
            'checked',
            'STATUS_POA',
            'comments',
        ],
    ]) ?>

</div>
