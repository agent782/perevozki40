<?php

use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\components\widgets\CheckDocuments;
use app\models\Document;
use yii\helpers\Url;
use app\components\widgets\ShowMessageWidget;
/* @var $this yii\web\View */
/* @var $searchModel app\models\DocumentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Договора с клиентами';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php
            $countNewDocs = $Documents->where(['status' => \app\models\Document::STATUS_ON_CHECKING])->count();
            if($countNewDocs):
        ?>
    <h3><a href="/document?DocumentSearch%5BcompanyName%5D=&DocumentSearch%5Bdate%5D=&DocumentSearch%5Bstatus%5D=2"> Новые договора <?= $countNewDocs ?></a></h3>
        <?php
            endif;
        ?>
    </p>
<?php //Pjax::begin(); ?><!--    -->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'companyName',
            'date',
//            [
//                'attribute' => 'type',
//                'filter' => \app\models\Document::getTypies(),
//                'value' => function($data){
//                    return  $data->typeString;
//                }
//            ],
//            'url_download:url',
//            'url_upload:url',
        [
            'attribute' => 'status',
            'format'=>'raw',
            'filter' => \app\models\Document::getStatuses(),
            'value' => function($data) {
//                return ($data->status === Document::STATUS_ON_CHECKING) ?
//                    $data->statusString .
//                    '<br>'
//                    . CheckDocuments::widget(['model' => $data, 'typeDocument' => Document::TYPE_CONTRACT_CLIENT])
//                    :
//                    $data->statusString;

                if ($data->status === Document::STATUS_ON_CHECKING) {
                    return
                        $data->statusString .
                        '<br>'
                        . CheckDocuments::widget(['model' => $data, 'typeDocument' => Document::TYPE_CONTRACT_CLIENT]);
                } else if ($data->status === Document::STATUS_SIGNED) {
                    return $data->statusString .
                        ' ' .
//                        '<img src = "/img/icons/download-25.png">' .
                        Html::a(Html::img('/img/icons/download-25.png'),
                            Url::to([
                                '/document/download-confirm-doc',
                                'id' => $data->id,
                                'type' => $data->type]),
                            ['class' => 'btn', 'data-toggle' => 'tooltip', 'title' => 'Скачать']) .
                        Html::a(Html::img('/img/icons/delete-26.png'), Url::to(['/document/delete-confirm-doc', 'id' => $data->id]),
                            ['class' => 'btn',
                                'data-confirm' => 'Удалить подписанный и подтвержденный Договор безвозвратно?'
                                , 'data-toggle' => 'tooltip', 'title' => 'Удалить']);
                } else if ($data->status === Document::STATUS_FAILED)  {
                    return $data->statusString
                    . ' '
                    . \app\components\widgets\ButtonUpload::widget([
                        'model' => $data,
                        'typeDocument' => $data->type,
                        'ToggleButton' => ['label' => Html::img('/img/icons/upload-24.png'), 'class' => 'btn',[ 'data-toggle' => 'tooltip', 'title' => 'Загрузить']],
                        'action' => \yii\helpers\Url::to([
                            'document/upload',
                            'id' => $data->id,
                            'completeRedirect' => '/document/index',
                            'type' => \app\models\Document::TYPE_CONTRACT_CLIENT,
                        ])
                    ])
                    . ' '
                    . ShowMessageWidget::widget([
                        'helpMessage' => $data->comments
                    ]);
                } else {
                    return $data->statusString
                        . ' '
                        . \app\components\widgets\ButtonUpload::widget([
                            'model' => $data,
                            'typeDocument' => $data->type,
                            'ToggleButton' => ['label' => Html::img('/img/icons/upload-24.png'), 'class' => 'btn',[ 'data-toggle' => 'tooltip', 'title' => 'Загрузить']],
                            'action' => \yii\helpers\Url::to([
                                'document/upload',
                                'id' => $data->id,
                                'completeRedirect' => '/document/index',
                                'type' => \app\models\Document::TYPE_CONTRACT_CLIENT,
                            ])
                        ]);

                }
            }
        ],
            // 'id_company',
            // 'id_vehicle',
            // 'id_user',
            // 'comments',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php //Pjax::end(); ?>
</div>
