<?php

use yii\bootstrap\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use app\components\widgets\AddOrEditReview;
use yii\web\YiiAsset;
use app\components\widgets\ShowMessageWidget;
/* @var $this yii\web\View */
/* @var $searchModel app\models\MessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Уведомления';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-index">

    <h3>
        <?= Html::encode($this->title) ?>
        <b class="incube"><?= \app\models\Message::countNewMessage(Yii::$app->user->id)?></b>
    </h3>
<bR>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?php \yii\widgets\Pjax::begin([
    'id' => 'pjax1'
]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'rowOptions' => [
            'onclick' => '$.pjax.reload({container: "#pjax_message"});'
        ],
        'responsiveWrap' => false,
        'beforeHeader' => Html::a('Пометить все как прочитанные',
            Url::to('/message/set-all-status-read')),
        'columns' => [
            [
                'attribute' => 'title',
                'contentOptions' => function($model){
                    return ($model->status != $model::STATUS_SEND)
                        ?
                        [
                            'style' => 'font-size: 10px;'
                        ]
                        :
                        [
                            'style' => 'font-size: 14px; font-weight:bold;',
                        ];
                },
                'format' => 'raw',
                'value' => function($model){

                    return Html::a($model->title, ['/message/view', 'id' => $model->id]);
                },


            ],
            [
                'attribute' => 'create_at',
            ],
            [
                'label' => 'Оценка',
                'format' => 'raw',
                'value' => function($model){
                    if($model->can_review_client) {
                        return AddOrEditReview::widget([
                            'message' => $model,
                            'id_user_from' => '',
                            'id_user_to' => '',
                            'redirect' => '',
                        ]);
                    }
                    if($model->can_review_vehicle) {
                        return AddOrEditReview::widget([
                            'message' => $model,
                            'id_user_from' => '',
                            'id_user_to' => '',
                            'redirect' => '',
                        ]);
                    }
                }
            ]
        ],
    ]); ?>
    <?php \yii\widgets\Pjax::end();?>
</div>