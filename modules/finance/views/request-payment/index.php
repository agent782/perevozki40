<?php

use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use app\models\RequestPayment;
/* @var $this yii\web\View */
/* @var $searchModel app\models\RequestPaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Заявки на выплаты водителям';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="request-payment-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'id_user',
            'cost',
            'type_payment',
            'requisites:ntext',
            //'url_files:ntext',
            'statusText',
            'create_at',
            [
                'label' => 'Действия',
                'format' => 'raw',
                'value' => function(RequestPayment $model){
                    $return = Html::a(Html::icon('ok-sign'),
                        Url::to(['/finance/request-payment/apply', 'id' => $model->id]));
                    return $return;
                }
            ]
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
