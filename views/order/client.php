
<?php
//use yii\grid\GridView;
use kartik\grid\GridView;
use app\models\Order;
use yii\bootstrap\Html;
use app\models\Vehicle;
use yii\helpers\Url;
use yii\bootstrap\Tabs;
/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Заказы';

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">
    <h4><?= Html::encode($this->title) ?></h4>
    <?= Html::a(Html::icon('plus') . ' Новый заказ', ['/order/create'], ['class' => 'btn btn-primary']);?>
    <?=
    Tabs::widget([
        'encodeLabels' => false,
        'items' => [
            [
                'label' => 'В поиске...<br>(+' . $dataProvider_newOrders->totalCount . ')',
                'content' => $this->render('client/new_orders', [
                    'dataProvider_newOrders' => $dataProvider_newOrders,
                    'searchModel' => $searchModel
                ]),
                'active' => ($tab == 'new')?true:false,
            ],
            [
                'label' => 'В процессе... <br>(+' . $dataProvider_in_process->totalCount . ')' ,
                'content' => $this->render('client/in_proccess', [
                    'dataProvider_in_process' => $dataProvider_in_process,
                    'searchModel' => $searchModel
                ]),
                'active' => ($tab == 'in_proccess')?true:false,            ],
            [
                'label' => 'Завершенные<br>(+' . $dataProvider_arhive->totalCount . ')',
                'content' => $this->render('client/finished', [
                    'dataProvider_arhive' => $dataProvider_arhive,
                    'searchModel' => $searchModel
                ]),
                'active' => ($tab == 'completed')?true:false,
            ],
            [
                'label' => 'Отмененные<br>(+' . $dataProvider_canceled->totalCount . ')',
                'content' => $this->render('client/canceled', [
                    'dataProvider_canceled' => $dataProvider_canceled,
                    'searchModel' => $searchModel
                ]),
                'active' => ($tab == 'canceled')?true:false,
            ],
        ]
    ]);
    ?>

</div>