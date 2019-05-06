<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CompanySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Companies';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Company', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'inn',
            'name',
            'address',
            'address_real',
            //'address_post',
            //'value',
            //'address_value',
            //'branch_type',
            //'capital',
            //'email:email',
            //'email2:email',
            //'email3:email',
            //'kpp',
            //'management_name',
            //'management_post',
            //'name_full',
            //'name_short',
            //'ogrn',
            //'ogrn_date',
            //'okpo',
            //'okved',
            //'opf_short',
            //'phone',
            //'phone2',
            //'phone3',
            //'citizenship',
            //'state_actuality_date',
            //'state_registration_date',
            //'state_liquidation_date',
            //'state_status',
            //'data_type',
            //'status',
            //'raiting',
            //'created_at',
            //'updated_at',
            //'FIO_contract',
            //'basis_contract',
            //'job_contract',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
