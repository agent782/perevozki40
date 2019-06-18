<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\settings\SettingSMSSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Setting Sms';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="setting-sms-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Setting Sms', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
//            'last_num_contract',
//            'noPhotoPath',
//            'FLAG_EXPIRED_ORDER',
//            'user_discount_cash',
            //'client_discount_cash',
            //'vip_client_discount_cash',
            //'user_discount_card',
            //'client_discount_card',
            //'vip_client_discount_card',
            //'procent_vehicle',
            //'procent_vip_vehicle',
            'sms_code_update_phone',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
