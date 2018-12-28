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
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'responsiveWrap' => false,
            'columns' => [
            [
                'class' => 'kartik\grid\ExpandRowColumn',
                'value' => function ($model, $key, $index, $column) {

                    return GridView::ROW_COLLAPSED;
                },
                'enableRowClick' => true,
                'allowBatchToggle'=>true,
                'detail'=>function ($model) {
                    return Yii::$app->controller->renderPartial('view', ['model'=>$model]);;
                },
//                'detailUrl' => function($model) {
//                    return Url::to(['/message/view', 'id' => $model->id]);
//                },
                'detailOptions'=>[
                    'class'=> 'kv-state-enable',
                ],
                ],
                [
                    'attribute' => 'title',
                    'contentOptions' => function($model){
                        return ($model->status != $model::STATUS_SEND)?
                            [
                                'class' => 'h1'
                            ]:
                            [
                                'class' => 'h4'
                            ];
                    }

                ],
                [
                    'attribute' => 'create_at',
                ],
                'status',
                [
                   'class' => \kartik\grid\ActionColumn::class,
                    'template' => '{delete}'
                ],
        ],
    ]); ?>
</div>
