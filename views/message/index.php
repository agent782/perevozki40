<?php

use yii\bootstrap\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\web\YiiAsset;
/* @var $this yii\web\View */
/* @var $searchModel app\models\MessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Сообщения';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-index">

    <h3><?= Html::encode($this->title) ?></h3>
<bR>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'responsiveWrap' => false,
            'columns' => [

                [
                    'attribute' => 'title',
                    'contentOptions' => function($model){
                        return ($model->status != $model::STATUS_SEND)?
                            [
                                'style' => 'font-size: 12px;'
                            ]:
                            [
                                'style' => 'font-size: 14px; font-weight:bold;'
                            ];
                    },
                    'format' => 'raw',
                    'value' => function($model){
                        return Html::a(
                            $model->title,
                            Url::to(['/message/view', 'id' => $model->id])
                        );
                    }
                ],
                [
                    'attribute' => 'create_at',
                ],
//                'status',
                [
                   'class' => \kartik\grid\ActionColumn::class,
                    'template' => '{delete}'
                ],
        ],
    ]); ?>
</div>