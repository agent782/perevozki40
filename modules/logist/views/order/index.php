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
        <?= Html::a('Новый заказ', ['/order/create'], ['class' => 'btn btn-success']) ?>
    </p>
        <?=
            \yii\bootstrap\Tabs::widget([
                'encodeLabels' => false,
                'items' => [
                    [
                        'label' => 'В поиске...<br>(+' . $dataProvider_newOrders->totalCount . ')',
                        'content' => $this->render('orders/new_orders', [
                            'dataProvider_newOrders' => $dataProvider_newOrders,
                            'searchModel' => $searchModel
                        ])
                    ],
                    [
                        'label' => 'В процессе... <br>(+' . $dataProvider_in_process->totalCount . ')' ,
                        'content' => $this->render('orders/in_proccess', [
                            'dataProvider_in_process' => $dataProvider_in_process,
                            'searchModel' => $searchModel
                        ])
                    ],
                    [
                        'label' => 'Завершенные<br>(+' . $dataProvider_arhive->totalCount . ')',
                        'content' => $this->render('orders/finished', [
                            'dataProvider_arhive' => $dataProvider_arhive,
                            'searchModel' => $searchModel
                        ])
                    ],
                    [
                        'label' => 'Отмененные <br>(+' . $dataProvider_canceled->totalCount . ')' ,
                        'content' => $this->render('orders/canceled',
                            ['dataProvider_canceled' => $dataProvider_canceled,
                                'searchModel' => $searchModel])
                    ],
                ]
            ])
        ?>

</div>
