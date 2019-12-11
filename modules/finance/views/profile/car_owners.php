<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 11.12.2019
 * Time: 9:41
 */
use kartik\grid\GridView;
use app\models\Profile;

?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'responsiveWrap' => false,
    'filterModel' => $modelSearch,
    'columns' => [
        'old_id',
        'id_user',
        'fioFull',
        [
            'label' => 'Баланс к выплате',
            'attribute' => 'balanceCarOwnerPayNow',
            'filter' => false

        ],
        [
            'label' => 'Баланс к выплате после проплат клиентами',
            'attribute' => 'balanceCarOwnerSum',
            'filter' => false

        ],

    ]
]);
?>
