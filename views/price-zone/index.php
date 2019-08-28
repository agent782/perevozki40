<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\components\widgets\ShowMessageWidget;
use app\models\PriceZone;
use kartik\export\ExportMenu;
/* @var $this yii\web\View */
/* @var $searchModel app\models\PriceZoneSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Тарифные зоны';
$this->params['breadcrumbs'][] = $this->title;

$actionColumn = [];
$addButton = '';
if(Yii::$app->user->can('admin')){
    $addButton = Html::a('Добавить тариф', ['create'], ['class' => 'btn btn-success']);
    $actionColumn = ['class' => 'yii\grid\ActionColumn'];
}
?>
<?php
$truck_columns = [
    'id',
    [
        'attribute' => 'bodyTypiesText',
        'label' => 'Типы кузова',
        'format' => 'raw',
        'value' => function(PriceZone $model){
            if($model->hasAllBodyTypies()) {
                return 'Любой'
//                            . ShowMessageWidget::widget([
//                                'helpMessage' => $model->bodiesColumn
//                            ])
                    ;
            } else {
                return $model->bodyTypiesText
                    . ' '
                    . ShowMessageWidget::widget([
                        'helpMessage' => $model->bodiesColumn
                    ]);
            }
        }
    ],
    [
        'attribute' => 'longlength',
        'label' => 'Груз-длинномер',
        'format' => 'raw',
        'value' => function($model){
            return ($model->longlength)? 'Да': 'Нет';
        }
    ],
    [
        'label' => 'Тоннаж (т.)',
        'format' => 'raw',
        'value' => function($model){
            return ($model->tonnage_max)?
                $model->tonnage_min
                . ' - '
                . $model->tonnage_max
//                        . ' т.'
                :
                '---'
                ;
        }
    ],
    [
        'label' => 'Длина кузова (м.)',
        'format' => 'raw',
        'value' => function(PriceZone $model){
            $return = '';
            if($model->length_max){
                $return .= $model->length_min
                    . ' - '
                    . $model->length_max;
//                          . ' м.';
                if($model->longlength){
                    $return .= ' *';
                }
            } else {
                $return .= '---';
            }
            return $return;
        }
    ],
    [
        'label' => 'Объем кузова (м3)',
        'format' => 'raw',
        'value' => function($model){
            return ($model->volume_max)?
                $model->volume_min
                . ' - '
                . $model->volume_max
//                        . ' м3.'
                :
                '---'
                ;
        }
    ],
    [
        'attribute' => 'r_km',
        'value' => function(PriceZone $model){
            return $model->getCosts_for()['r_km'];
        }
    ],
    'h_loading',
    [
        'attribute' => 'r_loading',
        'value' => function(PriceZone $model){
            return $model->getCosts_for()['r_loading'];
        }
    ],
    [
        'attribute' => 'min_price',
        'value' => function(PriceZone $model){
            return $model->getCosts_for()['min_price'];
        }
    ],
    [
        'attribute' => 'r_h',
        'value' => function(PriceZone $model){
            return $model->getCosts_for()['r_h'];
        }
    ],
    'min_r_10',
    'min_r_20',
    'min_r_30',
    'min_r_40',
    'min_r_50',
    [
        'attribute' => 'remove_awning',
        'value' => function(PriceZone $model){
            return $model->getCosts_for()['remove_awning'];
        }
    ],
    $actionColumn
];
$pass_columns = [
    'id',
    [
        'attribute' => 'bodyTypiesText',
        'label' => 'Типы кузова',
        'format' => 'raw',
        'value' => function(\app\models\PriceZone $model){
            if($model->hasAllBodyTypies()) {
                return 'Любой';
            } else {
                return $model->bodyTypiesText
                    . ' '
                    . ShowMessageWidget::widget([
                        'helpMessage' => $model->bodiesColumn
                    ]);
            }
        }
    ],
    'passengers',
    [
        'attribute' => 'r_km',
        'value' => function(PriceZone $model){
            return $model->getCosts_for()['r_km'];
        }
    ],
    'h_loading',
    [
        'attribute' => 'r_loading',
        'value' => function(PriceZone $model){
            return $model->getCosts_for()['r_loading'];
        }
    ],
    [
        'attribute' => 'min_price',
        'value' => function(PriceZone $model){
            return $model->getCosts_for()['min_price'];
        }
    ],
    [
        'attribute' => 'r_h',
        'value' => function(PriceZone $model){
            return $model->getCosts_for()['r_h'];
        }
    ],
    'min_r_10',
    'min_r_20',
    'min_r_30',
    'min_r_40',
    'min_r_50',
    $actionColumn        ];
$spec_columns = [
    'id',
    [
        'attribute' => 'bodyTypiesText',
        'label' => 'Типы кузова',
        'format' => 'raw',
        'value' => function(\app\models\PriceZone $model){
            if($model->hasAllBodyTypies()) {
                return 'Любой';
            } else {
                return $model->bodyTypiesText
                    . ' '
                    . ShowMessageWidget::widget([
                        'helpMessage' => $model->bodiesColumn
                    ]);
            }
        }
    ],
    [
        'label' => 'Тоннаж (т.)',
        'format' => 'raw',
        'value' => function($model){
            return ($model->tonnage_max)?
                $model->tonnage_min
                . ' - '
                . $model->tonnage_max
                :
                '---'
                ;
        }
    ],
    [
        'label' => 'Длина кузова (м.)',
        'format' => 'raw',
        'value' => function($model){
            return ($model->length_max)?
                $model->length_min
                . ' - '
                . $model->length_max
                :
                '---'
                ;
        }
    ],
    [
        'label' => 'Объем кузова (м3)',
        'format' => 'raw',
        'value' => function($model){
            return ($model->volume_max)?
                $model->volume_min
                . ' - '
                . $model->volume_max
                :
                '---'
                ;
        }
    ],
    [
        'attribute' => 'tonnage_spec_max',
        'label' => 'Грузоподъемность механизма (стрелы) (т.)',
        'format' => 'raw',
        'value' => function($model){
            return ($model->tonnage_spec_max)?
                $model->tonnage_spec_min
                . ' - '
                . $model->tonnage_spec_max
                :
                '---'
                ;
        }
    ],
    [
        'attribute' => 'lengrh_spec_max',
        'label' => 'Длина механизма (стрелы) (м.)',
        'format' => 'raw',
        'value' => function($model){
            return ($model->length_spec_max)?
                $model->length_spec_min
                . ' - '
                . $model->length_spec_max
                :
                '---'
                ;
        }
    ],
    [
        'attribute' => 'volume_spec_max',
        'label' => 'Объем механизма (ковша) (м3)',
        'format' => 'raw',
        'value' => function($model){
            return ($model->volume_spec)?
                $model->volume_spec
                :
                '---'
                ;
        }
    ],
    [
        'attribute' => 'r_km',
        'value' => function(PriceZone $model){
            return $model->getCosts_for()['r_km'];
        }
    ],
    'h_loading',
    [
        'attribute' => 'r_loading',
        'value' => function(PriceZone $model){
            return $model->getCosts_for()['r_loading'];
        }
    ],
    [
        'attribute' => 'min_price',
        'value' => function(PriceZone $model){
            return $model->getCosts_for()['min_price'];
        }
    ],
    [
        'attribute' => 'r_h',
        'value' => function(PriceZone $model){
            return $model->getCosts_for()['r_h'];
        }
    ],
    'min_r_10',
    'min_r_20',
    'min_r_30',
    'min_r_40',
    'min_r_50',
    $actionColumn
];
?>
<div class="container price-zone-index">
    <button class="btn btn-lg btn-danger">При расчете наличными с водителем - СКИДКА 9% !!!</button>

    <h4><?= Html::encode($this->title) ?></h4>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Рассчитать стоимость', '/order/create', ['class' => 'btn btn-primary'])?>
        <?= $addButton ?>
    </p>
    <div class="alert-warning">
        Обращаем Ваше внимание!
        На стоимость заказа напрямую влияют:
        <p>
            1. Общий пробег по маршруту (Начало и конец маршрута
            всегда устанавливается автоматически - г. Обнинск,ул.Борисоглебская, 88, далее пункты, указанные клиентом.
            Например: Клиенту надо перевезти груз из г.Боровск в г.Москва, расстояние будет рассчитано по маршруту:
            г.Обнинск - г.Боровск - г.Москва - г.Обнинск)
        </p>
        <p>
            2. Общее время на погрузку/разгрузку/ожидание (для заказов с пробегом более 120 км)
        </p>
        <p>
            3. Общее время работы (для заказов с пробегом менее 120 км)
        </p>
        <p>
            4. Количество "растентовок" кузова сверху или сбоку. Оплачиваются дополнительно.
        </p>
        <p>
            5. Дополнительные расходы. Платные дороги, въезды, выезды. Услуги грузчиков. Оплачиваются отдельно.
        </p>

    </p>
    <h4 >Грузовой транспорт</h4>
    <div>
        <?= ExportMenu::widget([
            'dataProvider' => $dataProviderTruck,
            'columns' => $truck_columns,
            'asDropdown' => false,
            'exportConfig' => [
                'Html' => false,
                'Csv' => false,
                'Txt' => false,
                'Xls' => false,
                'Pdf' => false,
                'Xlsx' => [
                    'label' => 'Скачать прайс-лист на услуги грузового транспорта (.xls)',
                ]
            ],
            'autoWidth' => false,
            'showConfirmAlert' => false,
            'filename' => 'perevozki40_price_truck_' . date('d_m_Y'),
        ]) ?>
    <?= GridView::widget([
        'dataProvider' => $dataProviderTruck,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'options' => [
            'style' => 'font-size: 10px'
        ],
        'columns' => $truck_columns,
    ]); ?>


    <h4>Пассажирский транспорт</h4>
        <?= ExportMenu::widget([
            'dataProvider' => $dataProviderPass,
            'columns' => $pass_columns,
            'asDropdown' => false,
            'exportConfig' => [
                'Html' => false,
                'Csv' => false,
                'Txt' => false,
                'Xls' => false,
                'Pdf' => false,
                'Xlsx' => [
                    'label' => 'Скачать прайс-лист на услуги пассажирского транспорта (.xls)',
                ]
            ],
            'autoWidth' => false,
            'showConfirmAlert' => false,
            'filename' => 'perevozki40_price_pass_' . date('d_m_Y'),

        ]) ?>
    <?= GridView::widget([
        'dataProvider' => $dataProviderPass,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'options' => [
            'style' => 'font-size: 10px'
        ],
        'columns' => $pass_columns,
    ]); ?>

    <h4>Спецтехника</h4>
        <?= ExportMenu::widget([
            'dataProvider' => $dataProviderSpec,
            'columns' => $spec_columns,
            'asDropdown' => false,
            'exportConfig' => [
                'Html' => false,
                'Csv' => false,
                'Txt' => false,
                'Xls' => false,
                'Pdf' => false,
                'Xlsx' => [
                    'label' => 'Скачать прайс-лист на услуги спецтехники (.xls)',
                ]
            ],
            'autoWidth' => false,
            'showConfirmAlert' => false,
            'filename' => 'perevozki40_price_spec_' . date('d_m_Y'),
        ]) ?>
    <?= GridView::widget([
        'dataProvider' => $dataProviderSpec,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'options' => [
            'style' => 'font-size: 10px'
        ],
        'columns' => $spec_columns,
    ]); ?>
    </div>
    <comment>
        <p>* В тарифах для груза-длинномера длина указана
            с учетом допустимого выступания груза по длине за габариты кузова на 2 метра.</p>
        <?php
            if(!Yii::$app->user->isGuest
                && !Yii::$app->user->can('user')
                && !Yii::$app->user->can('client')):
        ?>
        <p>** "Стоимость для водителя" ("Стоимость для Клиента без учета скидок")</p>
        <?php
            endif;
        ?>
    </comment>
</div>

