<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\models\Company;
use yii\helpers\Url;
use app\models\Document;
use app\components\widgets\ShowMessageWidget;

use app\components\widgets\CheckDocuments;


/* @var $this yii\web\View */
/* @var $searchModel app\models\CompanySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Организации и ИП';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Company', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
//        'pjax' => true,
        'responsiveWrap' => false,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'inn',
                'filter' => true
            ],
            [
                'attribute' => 'name',
                'filter' => \yii\jui\AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => 'name',
                    'clientOptions' => [
                        'source' => Company::find()->select(['name_full as value', 'name_full as label'])->asArray()->all(),
                        'autoFill' => true,
                    ]
                ])
            ],
            [
              'attribute' => 'inn',
                'filter' => false
            ],
            [
                'attribute' => 'balanceSum',
                'label' => 'Баланс',
                'format' => 'raw',
                'value' => function (Company $model){
                    $id_user = $model->getProfiles()->one()->id_user;
                    return Html::a($model->balanceSum, Url::to(['/admin/users/view', 'id' => $id_user]));
                },
                'filter' => false
//                'value' => function (Company $company){
//                    return $company->balance['balance'];
//                }
            ],
            [
                'label' => 'Договор',
                'format'=>'raw',
//                'filter' => \app\models\Document::getStatuses(),
                'value' => function(Company $model) {
                    $data = $model->contract;
                    if($data) {
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
                        } else if ($data->status === Document::STATUS_FAILED) {
                            return $data->statusString
                                . ' '
                                . \app\components\widgets\ButtonUpload::widget([
                                    'model' => $data,
                                    'typeDocument' => $data->type,
                                    'ToggleButton' => ['label' => Html::img('/img/icons/upload-24.png'), 'class' => 'btn', ['data-toggle' => 'tooltip', 'title' => 'Загрузить']],
                                    'action' => \yii\helpers\Url::to([
                                        'document/upload',
                                        'id' => $data->id,
                                        'completeRedirect' => '/finance/document/index',
                                        'type' => \app\models\Document::TYPE_CONTRACT_CLIENT,
                                    ])
                                ])
                                . ' '
                                . ShowMessageWidget::widget([
                                    'helpMessage' => $data->comments
                                ]);
                        } else {
                            return $data->statusString
                                . '<br>'
                                . Html::a('Скачать бланк',
                                    \yii\helpers\Url::to(['/company/download-document',
                                        'idCompany' => $model->id,
                                        'type' => \app\models\Document::TYPE_CONTRACT_CLIENT]),
                                    ['class'=>'btn btn-xs  btn-info'])
                                . ' '
                                . \app\components\widgets\ButtonUpload::widget([
                                    'model' => $data,
                                    'typeDocument' => $data->type,
                                    'ToggleButton' => ['label' => Html::img('/img/icons/upload-24.png'), 'class' => 'btn', ['data-toggle' => 'tooltip', 'title' => 'Загрузить']],
                                    'action' => \yii\helpers\Url::to([
                                        'document/upload',
                                        'id' => $data->id,
                                        'completeRedirect' => '/finance/document/index',
                                        'type' => \app\models\Document::TYPE_CONTRACT_CLIENT,
                                    ])
                                ]);
                        }
                    } else {
                        $data = new Document();
                        return Html::a('Скачать бланк',
                                \yii\helpers\Url::to(['/company/download-document',
                                    'idCompany' => $model->id,
                                    'type' => \app\models\Document::TYPE_CONTRACT_CLIENT]),
                                ['class'=>'btn btn-xs  btn-info'])
                            . ' '
                            .\app\components\widgets\ButtonUpload::widget([
                            'model' => $data,
                            'typeDocument' => Document::TYPE_CONTRACT_CLIENT,
                            'ToggleButton' => ['label' => Html::img('/img/icons/upload-24.png'), 'class' => 'btn', ['data-toggle' => 'tooltip', 'title' => 'Загрузить']],
                            'action' => \yii\helpers\Url::to([
                                '/document/upload',
                                'id' => $data->id,
                                'completeRedirect' => '/finance/document/index',
                                'type' => \app\models\Document::TYPE_CONTRACT_CLIENT,
                            ])
                        ]);
                    }
                }
            ],


//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
