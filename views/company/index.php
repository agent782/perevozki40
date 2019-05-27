<?php


use PhpOffice\PhpWord\PhpWord;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Widget;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use Breadlesscode\Office\Converter;
use kartik\file\FileInput;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use app\components\widgets\ShowMessageWidget;
use app\models\XprofileXcompany;
use yii\widgets\MaskedInput;
use kartik\alert\AlertBlock;
use yii\bootstrap\Alert;
use app\models\Document;
/* @var $this yii\web\View */
/* @var $searchModel app\models\CompanySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Юридические лица';
$this->params['breadcrumbs'][] = $this->title;





?>
<div class="company-index">


    <h2><?= Html::encode($this->title) ?></h2>
<br>
    <p>
        <?= Html::a('Добавить организацию или ИП', ['create'], ['class' => 'btn btn-success'])
        . '<br>'  ?>
    </p>
    <div class="text-warning" style="font-size: large">
        <?= '<br>' . Yii::$app->session->getFlash('maxCompanies') .  Yii::$app->session->getFlash('errorCreatePOA') ?>
    </div>
   <?=
        Alert::widget();
   ?>
<br>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'options' => [
          'class' => 'table table-bordered table-condensed',
        ],
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
//            'id',
            'inn',
            'name',
            [
//                'attribute'=>'parent_id',
                'label'=>'Договор',
                'format'=>'raw', // Возможные варианты: raw, html
                'value'=>function($data, $model, $modelDocument){
                    $modelDocument = \app\models\Document::findOne(['id_company' => $data->id, 'type' => \app\models\Document::TYPE_CONTRACT_CLIENT]);
                    if(!$modelDocument){
//                        $modelDocument = $data->createDocument(Document::TYPE_CONTRACT_CLIENT);

                        return '1. Скачайте заполненный бланк.<br>
                                2. Распечатайте, подпишите и заверьте печатью.<br>
                                3. Отсканируйте и отправьте Договор на проверку.<br><br>'
                            . Html::a('Скачать бланк',
                                \yii\helpers\Url::to(['download-document', 'idCompany' => $data->id, 'type' => \app\models\Document::TYPE_CONTRACT_CLIENT]),
                                ['class'=>'btn btn-info', 'data-toggle' => 'tooltip', 'title' => 'Инфорсация', 'id' => 'create-contract'])

                            ;
                    }
                    if ($modelDocument->url_upload && $modelDocument->status === \app\models\Document::STATUS_SIGNED){
                        return $modelDocument->getStatusString()
                            . ' '
                            . Html::a(Html::img('/img/icons/download-25.png'),
                                Url::to([
                                    '/document/download-confirm-doc',
                                    'id' => $data->ConfirmDoc->id,
                                    'type' => $data->ConfirmDoc->type]),
                                ['class' => 'btn', 'data-toggle' => 'tooltip', 'title' => 'Скачать'])
                            ;
                    } else if ($modelDocument->status === \app\models\Document::STATUS_ON_CHECKING) {
                        return $modelDocument->getStatusString()
                            .  Html::a(Html::img('/img/icons/delete-26.png'),
                                Url::to(['/document/delete-upload-docs', 'id' => $modelDocument->id, 'returnRedirectUrl' => '/company/index', 'type'=>\app\models\Document::TYPE_CONTRACT_CLIENT ]),
                                ['class' => 'btn',
                                    'data-confirm' => 'Удалить загруженный скан?',
                                    'data-toggle' => 'tooltip', 'title' => 'Удалить']);
                            ;
                    } else if($modelDocument->status === \app\models\Document::STATUS_FAILED){
                        return
                            $modelDocument->getStatusString()
                            . ' '
                            . ShowMessageWidget::widget([
                                'helpMessage' => $modelDocument->comments
                            ])
                            . '<br>'
                            . Html::a('Скачать бланк',
                                \yii\helpers\Url::to(['download-document', 'idCompany' => $data->id, 'type' => \app\models\Document::TYPE_CONTRACT_CLIENT]),
                                ['class'=>'btn btn-info', 'data-toggle' => 'tooltip', 'title' => 'Скачать бланк'])
                            . '<br><br>'
                            . \app\components\widgets\ButtonUpload::widget([
                                'model' => $modelDocument,
                                'typeDocument' => \app\models\Document::TYPE_CONTRACT_CLIENT,
                                'completeRedirect' => '/company/index',
                                'action' => \yii\helpers\Url::to([
                                    'document/upload',
                                    'id' => $modelDocument->id,
                                    'completeRedirect' => '/company/index',
                                    'type' => \app\models\Document::TYPE_CONTRACT_CLIENT,
                                ])
                            ])
                            ;
                    }
                    else {
                        return $modelDocument->getStatusString()
                                . '<br><br> '
                                . Html::a('Скачать бланк',
                                \yii\helpers\Url::to(['download-document', 'idCompany' => $data->id, 'type' => \app\models\Document::TYPE_CONTRACT_CLIENT]),
                                ['class'=>'btn btn-info'])
                                . '<br><br>'
                                . \app\components\widgets\ButtonUpload::widget([
                                        'model' => $modelDocument,
                                        'typeDocument' => \app\models\Document::TYPE_CONTRACT_CLIENT,
                                        'action' => \yii\helpers\Url::to([
                                            'document/upload',
                                            'id' => $modelDocument->id,
                                            'completeRedirect' => '/company/index',
                                            'type' => \app\models\Document::TYPE_CONTRACT_CLIENT,
                                        ])
                                ]);

                    }

                },
//                'filter' => Category::getParentsList()
            ],
            [
                'label'=>'Доверенность',
                'format'=>'raw', // Возможные варианты: raw, html
                'value'=>function($data){
                    $id_user = Yii::$app->user->getId();
                    $modelPOA = \app\models\XprofileXcompany::find()
                        ->where([
                           'id_company' => $data->id,
                        ])
                        ->andWhere([
                            'id_profile' => $id_user,
                        ])
                        ->one();

                    if($modelPOA->STATUS_POA===XprofileXcompany::STATUS_POWER_OF_ATTORNEY_UNSIGNED) {
                        return
                            $modelPOA->getSTATUS_POWER_OF_ATTORNEY()
                            . '<br><br>'
                            . \app\components\widgets\DownloadPOA::widget([
                                    'modelPOA' => $modelPOA,
                                ])
                            . '<br><br>'
                            . \app\components\widgets\ButtonUpload::widget([
                                'model' => $modelPOA,
                                'typeDocument' => \app\models\XprofileXcompany::ClientPOA,
                                'completeRedirect' => '/company/index',
                                'header' => 'Отправка подписанной доверенности',
                                'action' => \yii\helpers\Url::to(['/poa/upload-client-poa', 'idCompany' => $data->id, 'idUser' => $id_user, 'completeRedirect' => '/company/index']),
                            ]);
                    } else if ($modelPOA->STATUS_POA===XprofileXcompany::STATUS_POWER_OF_ATTORNEY_ON_CHECKING){
                        return
                            $modelPOA->getSTATUS_POWER_OF_ATTORNEY()
                            .  Html::a(Html::img('/img/icons/delete-26.png'),
                                Url::to(['/poa/delete-upload-poa', 'idCompany' => $data->id, 'idUser'=> $id_user,'redirect' => '/company']),
                                [
                                    'class' => 'btn',
                                    'data-confirm' => 'Удалить загруженный скан?',
                                    'data-toggle' => 'tooltip', 'title' => 'Удалить']);
                            ;
                    } else if ($modelPOA->STATUS_POA===XprofileXcompany::STATUS_POWER_OF_ATTORNEY_FAILED){
                        return
                            $modelPOA->getSTATUS_POWER_OF_ATTORNEY()
                            . ' '
                            . ShowMessageWidget::widget(['helpMessage' => $modelPOA->comments])
                            . '<br>'
                            .  \app\components\widgets\DownloadPOA::widget([
                                'modelPOA' => $modelPOA,
                            ])
                            . '<br><br>'
                            . \app\components\widgets\ButtonUpload::widget([
                                'model' => $modelPOA,
                                'typeDocument' => \app\models\XprofileXcompany::ClientPOA,
                                'completeRedirect' => '/company/index',
                                'header' => 'Отправка подписанной доверенности',
                                'action' => \yii\helpers\Url::to(['/poa/upload-client-poa', 'idCompany' => $data->id, 'idUser' => $id_user, 'completeRedirect' => '/company/index'])
                            ]);
                    } else if ($modelPOA->STATUS_POA===XprofileXcompany::STATUS_POWER_OF_ATTORNEY_SIGNED ){
                        return
                            $modelPOA->getSTATUS_POWER_OF_ATTORNEY()
                            . ' '
                            . Html::a(Html::img('/img/icons/download-25.png'),[
                                Url::to(['/poa/download-confirm-poa', 'idCompany' => $data->id, 'idProfile' => $id_user, 'return' => '/company'])
                            ])
                        ;
                    }
                }

            ],
            [
                'class' => \yii\grid\ActionColumn::className(),

                'urlCreator'=>function($action, $model, $key, $index){
                    return [$action,'id'=>$model->id, 'idUser' => Yii::$app->user->id, 'redirect' => Url::to(['/company'])];
                },
                'template'=>'{view} {update} {delete}',
            ]
        ],
    ]); ?>



    <?php

    ?>

</div>

<script>
    $('#create-contract').on('click', function () {
        setTimeout(function() {window.location.reload();}, 5000);
    })
</script>
