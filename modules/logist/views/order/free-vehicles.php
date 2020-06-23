<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 23.06.2020
 * Time: 10:36
 */
use kartik\grid\GridView;

?>

<title class="h4">
    Свободные ТС.
</title>

<?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'responsiveWrap' => false,
        'columns' => [
            [
                'attribute' => 'id_user',
//            'group' => true,
                'format' => 'raw',
                'value' => function(\app\models\Vehicle $model){
                    return '"' . $model->profile->old_id . '" ' .$model->profile->fioFull
                        . ' ' .  $model->profile->phone .' (ID ' . $model->profile->id_user . ')';
                },
            ],
            [
                'attribute' => 'fullInfo',
                'format' => 'raw'
            ],
        ]
    ])
?>
