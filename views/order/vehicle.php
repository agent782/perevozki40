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

    <?=
        Tabs::widget([
            'encodeLabels' => false,
            'items' => [
                [
                    'label' => 'В поиске...<br>(+' . $dataProvider_newOrders->getCount() . ')',
                    'content' => $this->render('vehicle/new_orders', [
                        'dataProvider_newOrders' => $dataProvider_newOrders,
                        'searchModel' => $searchModel
                    ])
                ],
                [
                    'label' => 'В процессе... <br>(+' . $dataProvider_in_process->getCount() . ')' ,
                    'content' => $this->render('vehicle/in_proccess', [
                        'dataProvider_in_process' => $dataProvider_in_process,
                        'searchModel' => $searchModel
                    ]),
                    'active' => ($dataProvider_in_process->count)?true:false,
                ],
                [
                    'label' => 'Завершенные<br>(+' . $dataProvider_arhive->getCount() . ')',
                    'content' => $this->render('vehicle/finished', [
                        'dataProvider_arhive' => $dataProvider_arhive,
                        'searchModel' => $searchModel
                    ]),
                ],
            ]
        ]);
    ?>

</div>
