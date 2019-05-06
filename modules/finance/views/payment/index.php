<?php

use yii\grid\GridView;
use yii\bootstrap\Html;
/* @var $this yii\web\View */
/* @var $searchModel app\models\PaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Расчеты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Html::icon('plus'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
//            'id',
            'date:date',
            [
                'label' => 'Дебет',
            ],
            [
                'label' => 'Кредит',
            ],
            [
                'label' => 'Контрагент',
            ],
            [
                'label' => 'Пользователь',
            ],
            'type',
//            'id_payer_user',
            //'id_recipient_user',
            //'id_payer_company',
            //'id_recipient_company',
            //'status',
            'comments:ntext',
            //'sys_info:ntext',
            //'create_at',
            //'update_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
