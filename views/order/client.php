<?php
use yii\grid\GridView;
use app\models\Order;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Заказы (Клиент)';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">
    <p>
        <?= Html::a('Сделать новый заказ', ['create'], ['class' => 'btn btn-lg btn-danger']) ?>
    </p>
    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php \yii\widgets\Pjax::begin([
            'id' => 'pjax'
        ]);

    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute' => 'statusText',
                'filter' => Html::activeCheckboxList($searchModel, 'statuses', \app\models\Order::getStatusesArray()),
                'contentOptions' => [
                    'class' => 'btn btn-block',
                    'style' => 'color: red;'
                ]
            ],
            [
                'attribute' => 'paidText',
                'filter' => Html::activeCheckboxList($searchModel, 'paid_statuses', [0 => 'Не оплачен', 1 => 'Оплачен']),
            ],
            'id_vehicle_type',
            'tonnage',
//            'length',
//            'width',
            // 'height',
            // 'volume',
            // 'longlength',
            // 'passengers',
            // 'ep',
            // 'rp',
            // 'lp',
            // 'tonnage_spec',
            // 'length_spec',
            // 'volume_spec',
            // 'cargo:ntext',
            'datetime_start',
            // 'datetime_finish:datetime',
            // 'datetime_access:datetime',
            'valid_datetime',
            'create_at',
            // 'id_route',
            // 'id_route_real',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php \yii\widgets\Pjax::end()?>

</div>
