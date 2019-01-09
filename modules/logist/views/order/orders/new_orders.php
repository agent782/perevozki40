<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 05.01.2019
 * Time: 12:28
 */
    use yii\grid\GridView;
?>

<?= GridView::widget([
    'dataProvider' => $dataProvider_newOrders,
    'filterModel' => $searchModel,
    'options' => ['class' => 'wrap'],
    'columns' => [
        'statusText',
        'id',
        [
            'attribute' => 'clientInfo',
            'format' => 'raw'
        ],
        'datetime_start',
        [
            'attribute' => 'shortRoute',
            'label' => 'Маршрут',
            'format' => 'raw'
        ],
        [
            'attribute' => 'shortInfoForClient',
            'format' => 'raw',
//            'contentOptions' => ['class' => 'truncate']
        ],
        'paymentText',
        'company.name',
        ['class' => 'yii\grid\ActionColumn'],
    ],
]); ?>
