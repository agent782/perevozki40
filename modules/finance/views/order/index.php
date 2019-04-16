<?php

use yii\bootstrap\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Журнал заказов';
?>
<div class="order-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pjax' => true,
        'options' => [
            'class' => 'minRoute'
        ],
        'columns' => [
            [
                'attribute' => 'id',
                'label' => '№ заказа'
            ],
            'paidText',
            [
                'attribute' =>'paymentText',
                'format' => 'raw'
            ],
            [
                'format' => 'raw',
                'attribute' => 'company.companyInfo'
            ],
            [
                'label' => 'Заказчик',
                'format' => 'raw',
                'value' => function($model){
                    return $model->profile->getProfileInfo(true, false, true);
                }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
