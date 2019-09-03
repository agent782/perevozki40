<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 27.06.2019
 * Time: 8:40
 */
/* @var $this \yii\web\View;
 *
 */
    use kartik\grid\GridView;
    use yii\bootstrap\Html;
    use yii\helpers\Url;

?>
    <label class="h3">Грузовые</label>
        <?=
            GridView::widget([
                'dataProvider' => $dataProviderTruck,
                'filterModel' => $searchModel,
                'responsiveWrap' => false,
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
                        'label' => 'Владелец',
                        'format' => 'raw',
                        'value' => function(\app\models\Vehicle $model){
                            $res ='';
                            $res .= '(ID ' . $model->id_user . ') ' . $model->profile->fioShort;
                            if($model->user->old_id){
                                $res .= '(' . $model->user->old_id . ')';
                            }
                            return Html::a(
                                    $res, Url::to(['/finance/profile/view', 'id' => $model->id_user]))
                            ;
                        }
                    ],
                    'regLicense.brand',
                    [
                        'label' => 'Т.',
                        'attribute' => 'tonnage'
                    ],
                    [
                        'attribute' => 'body_type',
                        'format' => 'raw',
                        'value' => function(\app\models\Vehicle $vehicle){
                            return $vehicle->getBodyTypeText(true, true);
                        }
                    ],
                    [
                        'attribute' => 'loadingtypesText',
                        'format' => 'raw',
                        'value' => function($vehicle){
                            return $vehicle->getLoadingTypesText(true);
                        }
                    ],
                    [
                        'attribute' => 'length',
                        'label' => 'Д'
                    ],
                    [
                        'attribute' => 'height',
                        'label' => 'В'
                    ],
                    [
                        'attribute' => 'width',
                        'label' => 'Ш'
                    ],
                    [
                        'attribute' => 'volume',
                        'label' => 'О'
                    ],
                    [
                        'label' => 'Дл.',
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
