<?php

use kartik\grid\GridView;
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
        var arr_ids = $(\'#ids_companies\').text().split(\' \');
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
    if($balance['user'] < 0 || $balance['car_owner'] < 0 || $balance['companies'] < 0) {
        $balanceCSS = 'color: red';
    }

    $items = [];
    if($dataProvider_car_owner){
        $items[] = [
            'label' => 'Ваш баланс водителя: ' . $balance['car_owner'] . 'р.',
            'content' => GridView::widget([
                'dataProvider' => $dataProvider_car_owner,
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
            ]),
        ];
    }
    if($dataProvider_user){
        $items[] = [
            'label' => 'Ваш баланс клиента: ' . $balance['user'] . 'р.',
            'content' => GridView::widget([
                'dataProvider' => $dataProvider_user,
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
            ]),
        ];
    }
    if($dataProviders_companies){
        $content = '';
        foreach ($dataProviders_companies as $id_company => $dataProvider_company){
            if($company = \app\models\Company::findOne($id_company)) {
                $content .= '<div class="h4" id="pointer_company_' . $id_company . '" style = "cursor: pointer;">';
                $content .= $company->name . ' ('
                . $Balance['balance_companies'][$id_company]['balance'] . ' р.)';
                $content .= '<div id="slide_company_' . $id_company . '">';
                $content .= GridView::widget([
                    'dataProvider' => $dataProvider_company,
                    'pjax' => true,
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
                                    $order = \app\models\Order::findOne($model['id_order']);
                                    if ($order) {
                                        if ($profile = $order->profile) {
                                            return $profile->fioFull;
                                        }
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
            'label' => 'Баланс Ваших юр. лиц: ' . $balance['companies'] . 'р.',
            'content' => $content
        ];
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


</div>
<div id="ids_companies" hidden><?=$ids_companies?></div>

<script>

</script>