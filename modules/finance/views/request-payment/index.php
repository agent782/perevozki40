<?php

use yii\bootstrap\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use app\models\RequestPayment;
use app\models\User;
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
        'responsiveWrap' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'create_at',
            [
                'attribute' => 'id_user',
                'label' => 'Водитель',
                'format' => 'raw',
                'value' => function(RequestPayment $model){
                    $user = User::findOne($model->id_user);
                    if($user){
                        return '"'. $user->old_id . '" ' . $user->profile->fioShort . ' (#' . $user->id . ')';
                    } else {
                        return '#' . $model->id_user;
                    }
                }
            ],
            'cost',
            'typePaymentText',
            'requisites:ntext',
            //'url_files:ntext',
            'statusText',

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
