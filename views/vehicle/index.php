<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\VehicleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Мой транспорт.';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs(
    '$("document").ready(function(){
        $("#aDeleted").on("click", function(){
            $("#deleted").slideToggle();
        });
    });'
);
?>
<div class="vehicle-index">

    <h2><?= Html::encode($this->title) ?></h2>

    <p>
        <?= Html::a('Добавить транспортное средство', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php
        if($dataProviderTruck->getCount()):
    ?>
    <h2>Грузовой транспорт</h2>
    <?php
        endif;
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProviderTruck,
        'filterModel' => $searchModel,
        'options' => [
            'style' => 'width: 70%;',
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => 'Тарифные зоны',
                'format' => 'raw',
                'value' => function($model) {
                    $return = '';
                    foreach ($model->price_zones as $price_zone) {
                        $return .= $price_zone->id . ', ';
                    }
                    $return = substr($return, 0, -2);
//                    $return .= '<br>' . Html::a('Изменить', Url::to(['/vehicle/select-pricezones', 'id' => $model->id]), ['class' => 'btn']);
                    return $return;
                }
            ],
            'regLicense.brand.brand',
            'regLicense.reg_number',
            [
                'label' => 'Грузоподъемность, т.',
                'contentOptions' => [
                    'style' => 'text-align: center;'
                ],
                'attribute' => 'tonnage'
            ],
            [
                'label' => 'Тип кузова',
                'value' => function($model){
                    return \app\models\BodyType::find()->where(['id' => $model->body_type])->one()->body;
                }
            ],
            [
                'label' => 'Размеры кузова (ДхШхВ / Объем) м.',
                'value' => function($model){
                    return $model->length . ' x ' . $model->width . ' x ' . $model->height . ' / ' . $model->volume;
                }
            ],
            [
                'label' => 'Тип погрузки',
                'format' => 'raw',
                'value' => function($model){
                    $res = '';
                    foreach ($model->loadingtypes as $loadingtype){
                        $res .= $loadingtype->type . '<br>';
                    }
                    return $res;
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
            // 'passengers',
            // 'ep',
            // 'rp',
            // 'lp',
            'statusText',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>


    <?php
    if($dataProviderPass->getCount()):
    ?>
    <h2>Пассажирский транспорт</h2>

    <?= GridView::widget([
        'dataProvider' => $dataProviderPass,
        'filterModel' => $searchModel,
        'summaryOptions' => ['style' => 'text-align: left;'],
        'options' => [
            'style' => 'width: 70%; text-align: center;',
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => 'Тарифные зоны',
                'format' => 'raw',
                'value' => function($model) {
                    $return = '';
                    foreach ($model->price_zones as $price_zone) {
                        $return .= $price_zone->id . ', ';
                    }
                    $return = substr($return, 0, -2);
//                    $return .= '<br>' . Html::a('Изменить', Url::to(['/vehicle/select-pricezones', 'id' => $model->id]), ['class' => 'btn']);
                    return $return;
                }
            ],
            'regLicense.brand.brand',
            'regLicense.reg_number',
            'passengers',
            [
                'label' => 'Тип кузова',
                'value' => function($model){
                    return \app\models\BodyType::find()->where(['id' => $model->body_type])->one()->body;
                }
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>

        <?php
            endif;
        ?>
    <?php
    if($dataProviderSpec->getCount()):
    ?>
    <h2>Спецтехника</h2>

    <?= GridView::widget([
        'dataProvider' => $dataProviderSpec,
        'filterModel' => $searchModel,
        'showOnEmpty' => true,
        'summaryOptions' => ['style' => 'text-align: left;'],
        'options' => [
            'style' => 'width: 70%; text-align:center;',
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => 'Тарифные зоны',
                'format' => 'raw',
                'value' => function($model) {
                    $return = '';
                    foreach ($model->price_zones as $price_zone) {
                        $return .= $price_zone->id . ', ';
                    }
                    $return = substr($return, 0, -2);
//                    $return .= '<br>' . Html::a('Изменить', Url::to(['/vehicle/select-pricezones', 'id' => $model->id]), ['class' => 'btn']);
                    return $return;
                }
            ],
            [
                'label' => 'Тип',
                'attribute' => 'bodyType.body'
            ],
            'regLicense.brand.brand',
            'regLicense.reg_number',
            'tonnage',
            'length',
            'width',
//             'height',
             'volume',
             'tonnage_spec',
             'length_spec',
            'volume_spec',
            // 'passengers',
            // 'ep',
            // 'rp',
            // 'lp',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>

        <?php
    endif;
    ?>

    <?php
    if($dataProviderDeleted->getCount()):
    ?>
<div>
    <a  href="#aDeleted" id="aDeleted"><h2> Удаленные</h2>(Показать/скрыть)</a>
        <div id="deleted" hidden>

        <?= GridView::widget([
        'dataProvider' => $dataProviderDeleted,
        'filterModel' => $searchModel,
        'summaryOptions' => ['style' => 'text-align: left;'],
        'options' => [
            'style' => 'width: 70%; text-align:center;',
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'regLicense.brand.brand',
            'regLicense.reg_number',
            [
                'label' => 'Тип',
                'value' => function($model){
                    return \app\models\BodyType::find()->where(['id' => $model->body_type])->one()->body;
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
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
                        $url = Url::toRoute(Url::to(['/vehicle/update', 'id' => $model->id]));
                        return Html::a('<span class="glyphicon glyphicon-edit"></span>',
                            $url, [
                                'title' => \Yii::t('yii', 'Восстановить/редактировать.'),
//                                'data-pjax' => '0',
                            ]);

                    }
                ],
                'template' => '{update} {delete}'
            ],
        ],
    ]);
        ?>

        </div>
        <?php
    endif;
    ?>
<!--    --><?//= Html::a('Delete selection', ['delete-selection'], ['class' => 'btn btn-success']) ?>
</div>
</div>

<!--<script>-->
<!--    $("document").ready(function(){-->
<!---->
<!--        $("input[name='selection[]']").on('click', function () {-->
<!---->
<!--            var selection = $("input[name='selection[]']");-->
<!--            $.each(selection, function(key, value) {-->
<!---->
<!--                if(value.checked){-->
<!--//                alert(value.checked);-->
<!--                    $('#deleteButton').prop("disabled", false);-->
<!--                    return false;-->
<!--                } else {-->
<!--//                    alert('2');-->
<!--                    $('#deleteButton').prop("disabled", true);-->
<!--                }-->
<!--            });-->
<!--        });-->
<!--        $("input[name='selection_all']").on('click', function () {-->
<!---->
<!--            var selection = $("input[name='selection[]']");-->
<!--            $.each(selection, function(key, value) {-->
<!---->
<!--                if(!value.checked){-->
<!--//                alert(value.checked);-->
<!--                    $('#deleteButton').prop("disabled", false);-->
<!--                    return false;-->
<!--                } else {-->
<!--//                    alert('2');-->
<!--                    $('#deleteButton').prop("disabled", true);-->
<!--                }-->
<!--            });-->
<!--        });-->
<!---->
<!---->
<!--    });-->
</script>