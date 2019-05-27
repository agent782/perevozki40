<?php

use yii\bootstrap\Html;
use kartik\checkbox\CheckboxX;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DriverSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $modelProfile \app\models\Profile */
/* @var $modelDriverLicense \app\models\DriverLicense */

$this->title = 'Мои водители.';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="driver-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php
        $form = \kartik\form\ActiveForm::begin();
    ?>
    <?=$form->field($modelProfile, 'is_driver')->widget(CheckboxX::class,[
        'id' => 'chk_is_driver',
        'labelSettings' => ['position' => CheckboxX::LABEL_RIGHT],
        'pluginOptions' => [
            'threeState' => false
        ]
    ])
        ->label('Я водитель', ['class' => 'h3'])
    ?>
    <?php
        $form::end();
    ?>
    <p>
        <?= Html::a('Добавить водителя.', ['/driver/create', 'id_car_owner' => Yii::$app->user->id], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
//            'name',
//            'surname',
//            'patronymic',
            [
                'attribute' => 'fio',
                'format' => 'html',
                'value' => function($model){
                    return ($model->status === 0)
                        ? '<del>'.$model->fio.'</del>'
                        : $model->fio;
                },
//                'contentOptions' => [
//                    'style' => ($model->status != 0)?'text-decoration: line-through':''
//                ]
            ],
//            'birthday',
            // 'address:ntext',
            // 'passport_id',
            // 'license_id',
             'phone',
            // 'phone2',
//             'raiting',
            'id',
            [
                'attribute' => 'status',
                'filter' => [
                    'Удален',
                    'Активен'
                ],
                'format' => 'raw',
                'value' => function ($model){
                    return $model->statusString;
                },
                'filterOptions' => [
                    'default' => 'Удален'
                ]
            ],
//            'status',
            // 'checking',
            // 'create_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'recovery' =>function ($url, $model) {
                        $url = Url::toRoute(Url::to(['/driver/recovery', 'id' => $model->id]));
                        return Html::a('<span class="glyphicon glyphicon-wrench"></span>',
                            $url, [
                                'title' => \Yii::t('yii', 'Восстановить'),
//                                'data-pjax' => '0',
                            ]);
                    },

                ],
                'visibleButtons' => [
                    'view' => function($model, $key, $index){
                        return $model->status != \app\models\Driver::STATUS_DELETED;
                    },
                    'update' => function($model, $key, $index){
                        return $model->status != \app\models\Driver::STATUS_DELETED;;
                    },
                    'delete' => function($model, $key, $index){
                        return $model->status != \app\models\Driver::STATUS_DELETED;;
                    },
                    'recovery' => function($model, $key, $index){
                        return $model->status === \app\models\Driver::STATUS_DELETED;;
                    },
                ],
                'template' => '{update} {delete} {recovery}'
            ],
        ],
    ]); ?>
</div>
