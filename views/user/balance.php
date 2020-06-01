<?php

use kartik\grid\GridView;
use app\models\Order;
use app\components\widgets\ShowMessageWidget;
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 10.07.2019
 * Time: 13:16
 */
/* @var $this \yii\web\View
 *
 */

    $this->registerJs(new \yii\web\JsExpression('
        $(function(){
        var arr_ids = $("#ids_companies").text().split(" ");
        for(var i in arr_ids) {
            $("#slide_company_" + arr_ids[i]).hide();
            $("#pointer_company_" + arr_ids[i]).click(function () {
                $(this).siblings()
                    .children()
                    .slideUp("slow");
                $(this)
                    .children()
                    .slideDown("slow");
            });

        }
    });

    '));
    $balanceCSS = 'color: red';
    if($balance) {
        if ($balance['user'] < 0 || $balance['car_owner'] < 0 || $balance['companies'] < 0) {
            $balanceCSS = 'color: red';
        }
    }
    $items = [];
    $comments = '';
    if($dataProvider_car_owner){
        $items[] = [
            'label' => 'Ваш баланс водителя*: ' . $balance['car_owner'] . 'р. (' . $balance['not_paid'] . '*****)' ,
            'content' => GridView::widget([
                'dataProvider' => $dataProvider_car_owner,
                'pjax'=>true,
                'pjaxSettings' => [
                    'options' => [
                        'id' => 'pjax_balance_car_owner'
                    ]
                ],
                'responsiveWrap' => false,
                'columns' => [
                    [
                        'label' => 'Дата',
                        'attribute' => 'date',
                    ],
                    [
                        'label' => 'Дебет',
                        'attribute' => 'debit',
                        'format' => 'raw',
                        'value' => function ($model){
                            return $model['debit'];
                        },
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
                    [
                        'label' => '',
                        'format' => 'raw',
                        'value' => function($model){
                            if(array_key_exists('id_order',$model)) {
                                $order = Order::findOne($model['id_order']);
                                if ($order) {
                                    return ShowMessageWidget::widget([
                                        'helpMessage' => $order->getFullFinishInfo(false, null,
                                            true, true)
                                    ]);
                                }
                            }
                        }
                    ]
                ]
            ]),
        ];
        $comments .= ($balance['car_owner'] - $balance['not_paid'])
            ? 'Сумма к выплате на сегодняшний день: '
            . ($balance['car_owner'] - $balance['not_paid']) . ' р.'
            : 'Сумма к выплате на сегодняшний день: 0р.';
        $comments .= '<p><comment>* Баланс по принятым Вами заказам на Ваши ТС.</comment></p>';
        $comments .= '<p><comment>**** Сумма к выплате за заказ
            (Оставшаяся неоплаченная Клиентом сумма по заказу)</comment></p>';
        $comments .= '<p>***** Сумма неоплаченных Клиентами "безнальных" заказов</p>';
    }
    if($dataProvider_user && $balance){
        $items[] = [
            'label' => 'Ваш баланс клиента**: ' . $balance['user'] . 'р.',
            'content' => GridView::widget([
                'dataProvider' => $dataProvider_user,
                'pjax'=>true,
                'pjaxSettings' => [
                    'options' => [
                        'id' => 'pjax_balance_client'
                    ]
                ],
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
                    [
                        'label' => '',
                        'format' => 'raw',
                        'value' => function($model){
                            if(array_key_exists('id_order',$model)) {
                                $order = Order::findOne($model['id_order']);
                                if ($order) {
                                    return ShowMessageWidget::widget([
                                        'helpMessage' => $order->getFullFinishInfo(false, null, true, true)
                                    ]);
                                }
                            }
                        }
                    ]
                ]
            ]),
        ];
        $comments .= '<p><comment>** Баланс по Вашим заказам за наличный расчет и с оплатой банковской картой</comment></p>';
    }
    if($dataProviders_companies){
        $content = '';
        foreach ($dataProviders_companies as $id_company => $dataProvider_company){
            if($company = \app\models\Company::findOne($id_company)) {
                $content .= '<div id="pointer_company_' . $id_company . '" style = "cursor: pointer;">';
                $content .= $company->name . '('
                . $Balance['balance_companies'][$id_company]['balance'] . ' р.)';
                $content .= '<div id="slide_company_' . $id_company . '">';
                $content .= GridView::widget([
                    'dataProvider' => $dataProvider_company,
                    'pjax'=>true,
                    'pjaxSettings' => [
                        'options' => [
                            'id' => 'pjax_balance_company_' . $company->id
                        ]
                    ],
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
                        [
                            'label' => 'Кто заказывал',
                            'value' => function($model){
                                if(array_key_exists('id_order',$model)) {
                                    $order = Order::findOne($model['id_order']);
                                    if ($order) {
                                        if ($profile = $order->profile) {
                                            return $profile->fioFull;
                                        }
                                    }
                                }
                            }
                        ],
                        [
                            'label' => '',
                            'format' => 'raw',
                            'value' => function($model){
                                if(array_key_exists('id_order',$model)) {
                                    $order = Order::findOne($model['id_order']);
                                    if ($order) {
                                        return ShowMessageWidget::widget([
                                            'helpMessage' => $order->getFullFinishInfo(false, null, true, true)
                                        ]);
                                    }
                                }
                            }
                        ]
                    ]
                ]);
                $content .= '</div> </div>';
            }
        }
        $items[] = [
            'label' => 'Баланс Ваших юр. лиц***: ' . $balance['companies'] . 'р.',
            'content' => $content
        ];
        $comments .= '<p><comment>*** Баланс по заказам с оплатой по безналичному расчету 
от Ваших юр. лиц. В том числе заказы, сделанные другими сотрудниками Ваших юр. лиц.</comment></p>';
    }

?>

<div class="container">
    <?=
        \yii\bootstrap\Tabs::widget([
            'id' => 'balances',
           'encodeLabels' => false,
           'headerOptions' => [
               'class' => 'h4',
               'style' => $balanceCSS
           ],
           'items' => $items,
        ]);
    ?>
    <br><br>
    <?= $comments?>

</div>
<div id="ids_companies" hidden><?=$ids_companies?></div>

<script>

</script>