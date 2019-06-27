<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 27.06.2019
 * Time: 8:40
 */
use kartik\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
?>

<!--    <label class="h3">Пассажирские</label>-->
    <?=
    GridView::widget([
        'dataProvider' => $dataProviderPass,
//            'filterModel' => $SeachModel,
        'options' => [
            'style' => 'width: 70%;'
        ],
        'columns' => [
            ['class' => '\yii\grid\SerialColumn'],
            [
                'label' => 'Пользователь',
                'format' => 'raw',
                'value' => function($model){
                    $res ='';
                    $res .= $model->id_user . ' ' . $model->profile->fioShort;
                    return $res;
                }
            ],
            'regLicense.brand',
            'passengers',
            'tonnage',
            'bodyTypeText',
            [
                'attribute' => 'status',
                'label' => 'Статус',
                'format' => 'raw',
                'value' => function($model){
                    $res = $model->statusText . '<br>';
                    $res .= Html::a(
                        'Проверить',
                        Url::to(['check', 'id_vehicle' => $model->id]),
                        ['class' => 'btn-xs btn-success']
                    );
                    return $res;

                }
            ],
            [
                'class' => \kartik\grid\ActionColumn::class,
                'buttons' => $ActionColumnButtons,
                'template' => '{view} {update} {delete}'
            ]
        ]
    ])

    ?>
