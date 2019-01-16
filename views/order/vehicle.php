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

$this->title = 'Заказы (Водитель)';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">
    <h4><?= Html::encode($this->title) ?></h4>
    <?php
        $Tab = Tabs::begin();
        $Tab->encodeLabels = false;
        $Tab->items = [
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
        ];
        Tabs::end();
    ?>

</div>
