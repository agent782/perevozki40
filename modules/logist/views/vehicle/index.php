<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 02.10.2018
 * Time: 14:48
 */
use yii\bootstrap\Html;
use kartik\grid\GridView;
use yii\bootstrap\Widget;
use app\components\widgets\CheckVehicleWidget;
use yii\helpers\Url;
use yii\bootstrap\Tabs;

$this->title = Html::encode('ТС');

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
<div>
    <h3>
        <?=$this->title?>
        <?= Html::a(Html::icon('plus', ['class' => 'btn btn-primary']),
                Url::to([
                    '/user/find-user',
                    'redirect' => '/vehicle/create',
                    'redirect2' => '/logist/vehicle'
                ])
            );
        ?>
    </h3>
</div>
    <?=
        Tabs::widget([
            'encodeLabels' => false,
            'items' => [
                [
                    'label' => 'Грузовые',
                    'content' => $this->render('truck', [
                        'searchModel' => $searchModel,
                        'dataProviderTruck' => $dataProviderTruck,
                        'ActionColumnButtons' => $ActionColumnButtons
                    ])
                ],
                [
                    'label' => 'Пассажирские',
                    'content' => $this->render('pass', [
                        'searchModel' => $searchModel,
                        'dataProviderPass' => $dataProviderPass,
                        'ActionColumnButtons' => $ActionColumnButtons
                    ])
                ],
                [
                    'label' => 'Спецтехника',
                    'content' => $this->render('spec', [
                        'searchModel' => $searchModel,
                        'dataProviderSpec' => $dataProviderSpec,
                        'ActionColumnButtons' => $ActionColumnButtons
                    ])
                ],
                [
                    'label' => 'Удаленные',
                    'content' => $this->render('deleted', [
                        'searchModel' => $searchModel,
                        'dataProviderDeleted' => $dataProviderDeleted,
                        'ActionColumnButtons' => $ActionColumnButtons
                    ])
                ]
            ],
            'options' => ['tag' => 'div'],
            'itemOptions' => ['tag' => 'div'],
        ]);
    ?>
