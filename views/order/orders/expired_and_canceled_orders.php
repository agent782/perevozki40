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
    'dataProvider' => $dataProvider_expired_and_canceled,
    'filterModel' => $searchModel,
    'columns' => [
//        ['class' => 'yii\grid\SerialColumn'],
        'id',
        'statusText',
        'id_user',
        'id_company',
        'id_vehicle_type',
        'tonnage',
        ['class' => 'yii\grid\ActionColumn'],
    ],
]); ?>
