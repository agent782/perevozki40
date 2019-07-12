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
        'responsiveWrap' => false,
//            'filterModel' => $SeachModel,
        'options' => [
            'style' => 'width: 70%;'
        ],
        'columns' => [
            [
                'attribute' => 'id',
                'format' => 'raw',
                'value' => function($vehicle){
                    return Html::a(
                        $vehicle->id,
                        ['/logist/vehicle/view', 'id' => $vehicle->id]
                    );
                }
            ],
            [
                'label' => 'Пользователь',
                'format' => 'raw',
                'value' => function($model){
                    $res ='';
                    $res .= $model->id_user . ' ' . $model->profile->fioShort;
                    return $res;
                }
            ],
            'passengers',
            [
                'attribute' => 'body_type',
                'format' => 'raw',
                'value' => function(\app\models\Vehicle $vehicle){
                    return $vehicle->getBodyTypeText(true, true);
                }
            ],
            'tonnage',
            'regLicense.brand',
            'regLicense.reg_number',

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
