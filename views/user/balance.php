<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 10.07.2019
 * Time: 13:16
 */
    $balanceCSS = ($balance<0)?'color: red':'color: green';
?>

<div class="container">
    <h3 style="<?=$balanceCSS?>">
        <b>Ваш баланс: <?= $balance?> р.</b>
    </h3>
    (С учетом неоплаченных заказов Клиентами: <?= $balance + $balance_not_paid?> р.)
    <br><br>

    <?php
    echo \kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'responsiveWrap' => false,
        'columns' => [
            [
                'label' => 'Дата',
                'attribute' => 'date',
            ],
            [
                'label' => 'Дебет',
                'attribute' => 'debit',
                'contentOptions' => ['style' => 'color: green']
            ],
            [
                'label' => 'Кредит',
                'attribute' => 'credit',
                'contentOptions' => ['style' => 'color: red']
            ],
            [
                'label' => 'Комментарий',
                'attribute' => 'description',
            ],
        ]
    ]);

    ?>

</div>
