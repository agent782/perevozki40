<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 27.06.2019
 * Time: 8:41
 */
use kartik\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Vehicle;
?>

    <label class="h3">Спецтехника</label>
    <?=
    GridView::widget([
        'dataProvider' => $dataProviderSpec,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'pjax' => true,
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
            [
                'attribute' => 'body_type',
                'format' => 'raw',
                'value' => function(\app\models\Vehicle $vehicle){
                    return $vehicle->getBodyTypeText(true, true);
                },
                'filter' =>
                    Html::activeCheckboxList($searchModel, 'body_typies',
                        ArrayHelper::map(\app\models\BodyType::find()
                            ->where(['in', 'id',
                                [Vehicle::BODY_manipulator, Vehicle::BODY_dump,
                                    Vehicle::BODY_crane, Vehicle::BODY_excavator, Vehicle::BODY_excavator_loader]
                            ])-> all(),'id', 'body')
                    )
                ,
                'filterOptions' => ['class' => 'minRoute'],
            ],
            'regLicense.brand',
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
