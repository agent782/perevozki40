<?php

use yii\bootstrap\Html;
use kartik\grid\GridView;
use app\models\News;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\NewsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Новости';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="news-index">

    <h2><?= Html::encode($this->title) ?></h2>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php if(Yii::$app->user->can('admin'))
            echo Html::a('Добавить новость', ['create'], ['class' => 'btn btn-xs btn-success']);
        ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'title',
                'format' => 'raw',
                'contentOptions' => [
                    'class' => 'h3'
                ],
                'value' => function (News $model){
                    return Html::a($model->title, Url::to(['/news/view', 'id' => $model->id]), [
//                        'onclick' => 'alert()'
                    ]);
                }
            ],
//            [
//                'attribute' => 'text',
//                'format' => 'raw',
//            ],
            [
                'attribute' => 'create_at',
                'filter' => false
            ],
//            [
//                'attribute' => 'views',
//                'filter' => false
//            ],
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
