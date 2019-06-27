<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 27.06.2019
 * Time: 9:09
 */
use kartik\grid\GridView;
?>

    <label class="h3">Удаленные</label>

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
                'regLicense.brand.brand',
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
