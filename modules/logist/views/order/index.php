<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'ЗАКАЗЫ';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">
    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Новый заказ', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<!--    <a class = "btn btn-link left"><h3>В поиске  ( + --><?//= $countNewOrders ?><!--)</h3></a>-->
    <div id="new_orders">
        <?=
            \yii\bootstrap\Tabs::widget([
                'encodeLabels' => false,
                'items' => [
                    [
                        'label' => 'В поиске...<br>(+' . $dataProvider_newOrders->getCount() . ')',
                        'content' => $this->render('orders/new_orders', [
                            'dataProvider_newOrders' => $dataProvider_newOrders,
                            'searchModel' => $searchModel
                        ])
                    ],
                    [
                        'label' => 'В процессе... <br>(+' . $dataProvider_in_process->getCount() . ')' ,
                    ],
                    [
                        'label' => 'Архив<br>(+' . $dataProvider_arhive->getCount() . ')',
                    ],
                    [
                        'label' => 'Отмененные <br>(+' . $dataProvider_expired_and_canceled->getCount() . ')' ,
                        'content' => $this->render('orders/expired_and_canceled_orders',
                            ['dataProvider_expired_and_canceled' => $dataProvider_expired_and_canceled,
                                'searchModel' => $searchModel])
                    ],
                ]
            ])
        ?>



</div>
</div>