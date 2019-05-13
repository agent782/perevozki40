<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 13.05.2019
 * Time: 12:29
 */

    echo \kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute' => 'paidText',
                'visible' => $visiblePaid
            ],
            [
                'attribute' => 'paidCarOwnerText',
                'visible' => $visiblePaidCarOwner
            ],
            [
                'attribute' => 'invoice.number',
                'visible' => $visibleInvoice
            ],
            [
                'attribute' => 'invoice.status',
                'visible' => $visibleInvoice
            ],
        ]
    ])

?>



