<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 02.10.2018
 * Time: 14:48
 */
use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\bootstrap\Widget;
use app\components\widgets\CheckVehicleWidget;
use yii\helpers\Url;

$this->title = Html::encode('ТС');
$this->registerJs(
    '$(function(){
        $("#aTruck").on("click", function(){
            $("#div_truck").slideToggle();
        });
        $("#aPass").on("click", function(){
            $("#div_pass").slideToggle();
        });
        $("#aSpec").on("click", function(){
            $("#div_spec").slideToggle();
        });
                $("#aDeleted").on("click", function(){
            $("#div_deleted").slideToggle();
        });
    });'
);

$ActionColumnButtons =[
    'delete' =>function ($url, $model) {
        $url = Url::toRoute(Url::to(['/vehicle/full-delete', 'id' => $model->id]));
        return Html::a('<span class="glyphicon glyphicon-trash"></span>',
            $url, [
                'title' => \Yii::t('yii', 'Удалить безвозвратно.'),
                'data-confirm' => Yii::t('yii', 'ТС будет удалено безвозвратно, без возможности восстановления!'),
                'data-method' => 'post',
            ]);
    },
    'update' => function ($url, $model) {
        $url = Url::toRoute(Url::to(['/vehicle/update', 'id' => $model->id, 'redirect' => '/logist/vehicle']));
        return Html::a('<span class="glyphicon glyphicon-edit"></span>',
            $url, [
                'title' => \Yii::t('yii', 'Восстановить/редактировать.'),
//                                'data-pjax' => '0',
            ]);

    },
    'view' => function($url,$model) {
        $url = Url::toRoute(Url::to(['/vehicle/view', 'id' => $model->id]));
        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',
                $url, [
                    'title' => 'Просмотр',
            ]);
    }
];

?>
<div class="container-fluid">
    <h2>
        <?=$this->title?>
        <?= Html::a(Html::icon('plus', ['class' => 'btn btn-primary']),
                Url::to([
                    '/user/find-user',
                    'redirect' => '/vehicle/create',
                    'redirect2' => '/logist/vehicle'
                ])
            );
        ?>


    </h2>

    <a  href="#aTruck" id="aTruck"><h3>Грузовые ТС</h3></a>
    <div id="div_truck">
        <?=
            GridView::widget([
                'dataProvider' => $dataProviderTruck,
//                'filterModel' => $SeachModel,
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
                    'tonnage',
                    'bodyTypeText',
                    [
                        'attribute' => 'loadingtypesText',
                        'format' => 'raw'
                    ],
                    [
                        'label' => 'Размеры кузова (ДхШхВ / Объем) м.',
                        'value' => function($model){
                            return $model->length . ' x ' . $model->width . ' x ' . $model->height . ' / ' . $model->volume;
                        }
                    ],
                    [
                        'label' => 'Груз-длинномер',
                        'format' => 'raw',
                        'contentOptions' => [
                            'style' =>'text-align: center;'
                        ],
                        'value' => function($model){
                            return
                                ($model->longlength)?
                                    \yii\bootstrap\Html::img('/img/icons/yes-20.png', ['title' => 'Да']):
                                    \yii\bootstrap\Html::img('/img/icons/no-20.png', ['title' => 'Нет'])
                                ;
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'label' => 'Статус',
                        'format' => 'raw',
                        'value' => function($model){
                            $res = $model->statusText . '<br>';
                            $res .= CheckVehicleWidget::widget(['modelVehicle' => $model]);
                            return $res;

                        }
                    ],
                    [
                        'class' => \yii\grid\ActionColumn::className(),
                        'buttons' => $ActionColumnButtons,
                        'template' => '{view} {update} {delete}'
                    ]
                ]
            ])
        ?>
    </div>

    <a  href="#aPass" id="aPass"><h3>Пассажирские ТС</h3></a>
    <div id="div_pass">
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
                        $res .= CheckVehicleWidget::widget(['modelVehicle' => $model]);
                        return $res;

                    }
                ]


            ]
        ])

        ?>
    </div>

    <a  href="#aSpec" id="aSpec"><h3>Спецтехника</h3></a>
    <div id="div_spec" >
<?=
        GridView::widget([
            'dataProvider' => $dataProviderSpec,
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
                'bodyTypeText',
                'regLicense.brand.brand',
                [
                    'attribute' => 'status',
                    'label' => 'Статус',
                    'format' => 'raw',
                    'value' => function($model){
                        $res = $model->statusText . '<br>';
                        $res .= CheckVehicleWidget::widget(['modelVehicle' => $model]);
                        return $res;

                    }
                ]


            ]
        ])

        ?>
    </div>

    <a  href="#aDeleted" id="aDeleted"><h3> Удаленные</h3></a>
    <div id="div_deleted" hidden>
<?=
        GridView::widget([
            'dataProvider' => $dataProviderDeleted,
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
                'tonnage',
                'bodyTypeText',
                [
                    'attribute' => 'loadingtypesText',
                    'format' => 'raw'
                ],
                [
                    'label' => 'Размеры кузова (ДхШхВ / Объем) м.',
                    'value' => function($model){
                        return $model->length . ' x ' . $model->width . ' x ' . $model->height . ' / ' . $model->volume;
                    }
                ],
                [
                    'label' => 'Груз-длинномер',
                    'format' => 'raw',
                    'contentOptions' => [
                        'style' =>'text-align: center;'
                    ],
                    'value' => function($model){
                        return
                            ($model->longlength)?
                                \yii\bootstrap\Html::img('/img/icons/yes-20.png', ['title' => 'Да']):
                                \yii\bootstrap\Html::img('/img/icons/no-20.png', ['title' => 'Нет'])
                            ;
                    }
                ],
                'statusText',
            ]
        ])

        ?>
    </div>
</div>