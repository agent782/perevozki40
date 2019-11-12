<?php

use yii\bootstrap\Html;
use yii\grid\GridView;
use app\models\RequestPayment;
use app\components\widgets\ShowMessageWidget;

/* @var $this yii\web\View */
/* @var $searchModel app\RequestPaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Выплаты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="request-payment-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Новый запрос', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'create_at',
            'cost',
            [
                'label' => 'Тип оплаты',
                'format' => 'raw',
                'value' => function (RequestPayment $model){
                    return $model->getTypePaymentText(true)
                        . ShowMessageWidget::widget([
                           'helpMessage' => $model->requisites
                        ]);
                }
            ],
            'statusText',
            [
                'label' => 'Действия',
                'format' => 'raw',
                'value' => function(RequestPayment $model){
                    return Html::a(Html::icon('remove'),
                        ['/request-payment/cancel', 'id' => $model->id],
                        ['title' => 'Отменить запрос', 'data-confirm' => 'Отменить заявку?']
                    );
                }
            ]
//            'requisites:ntext',
            //'url_files:ntext',
            //'status',

//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
