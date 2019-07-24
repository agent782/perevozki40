<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 10.07.2019
 * Time: 13:16
 */
//var_dump($dataProvider);
    $balanceCSS = ($balance<0)?'color: red':'color: green';
?>

<div class="container">

    <?php if(Yii::$app->user->can('car_owner')): ?>
        <h3 style="<?=$balanceCSS?>">
            <b>Ваш баланс водителя: <?= $balance['car_owner']?> р.</b>
        </h3>
    (С учетом неоплаченных заказов Клиентами: <?= $balance['car_owner'] + $balance['not_paid']?> р.)
    <br><br>
    <?php endif;?>
    <h3 style="<?=$balanceCSS?>">
        <b>Ваш баланс клиента: <?= $balance['user']?> р.</b>
    </h3>


    <?php
//    echo \kartik\grid\GridView::widget([
//        'dataProvider' => $dataProvider,
//        'responsiveWrap' => false,
//        'columns' => [
//            [
//                'label' => 'Дата',
//                'attribute' => 'date',
//            ],
//            [
//                'label' => 'Дебет',
//                'attribute' => 'debit',
//                'contentOptions' => ['style' => 'color: green']
//            ],
//            [
//                'label' => 'Кредит',
//                'attribute' => 'credit',
//                'contentOptions' => ['style' => 'color: red']
//            ],
//            [
//                'label' => 'Комментарий',
//                'attribute' => 'description',
//            ],
//        ]
//    ]);

    ?>

</div>
