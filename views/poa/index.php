<?php

use yii\grid\GridView;
use app\models\XprofileXcompany;
use yii\helpers\Url;
use yii\bootstrap\Html;
use app\components\widgets\ShowMessageWidget;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchXprofileXcompany */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Доверенности';
$this->params['breadcrumbs'][] = $this->title;

\yii\helpers\Url::remember();

?>

<div class="xprofile-xcompany-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?php
        $countNewDocs = $POA->where(['STATUS_POA' => \app\models\XprofileXcompany::STATUS_POWER_OF_ATTORNEY_ON_CHECKING])->count();
        if($countNewDocs):
        ?>
    <h3>
        <a href="/poa/index?SearchXprofileXcompany%5Bjob_post%5D=&SearchXprofileXcompany%5Bterm_of_office%5D=&SearchXprofileXcompany%5BSTATUS_POA%5D=2&sort=job_post">
            Доверенности на проверку: <?= $countNewDocs ?>
        </a>
    </h3>
    <a href="/poa" class="btn btn-info">Сбросить фильтры</a>
    <?php
    endif;
    ?>
    </p>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
   <div class="text-warning" style="font-size: large">
        <?= '<br>' . Yii::$app->session->getFlash('flashMes')?>
   </div>
    <p>

    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

//            ['class' => 'yii\grid\SerialColumn'],
//            'profile.fioShort',
//            'id_company',
          [
                'attribute' => 'fio',
                'label'=>'ФИО',
                'format'=>'raw', // Возможные варианты: raw, html
//                'filter' => \app\models\XprofileXcompany::getStatuses(),
                'value'=>function($data) {
                    $modelPOA = $data;
                    return
                        Html::a(
                                $modelPOA->fio . ' (' . $modelPOA->id_profile . ')',
                                Url::to(['/admin/users/update' , 'id' => $modelPOA->id_profile, 'redirect' => Url::to(['/poa']) ])
                            );
//                        Url::to(['/user/view' , 'id' => $modelPOA->id_profile]) .
//                        $modelPOA->getFIO()
//                        . ' (' . $modelPOA->id_profile . ')'
//                    ;
                }
            ],
//            'companyName',
            [
                'attribute' => 'companyName',
                'label'=>'Организвция или ИП',
                'format'=>'raw', // Возможные варианты: raw, html
//                'filter' => \app\models\XprofileXcompany::getStatuses(),
                'value'=>function($data) {
                    $modelPOA = $data;
                    return
                        Html::a(
                            $modelPOA->companyName . ' (' . $modelPOA->id_company . ')',
                            Url::to(['/company/update' , 'id' => $modelPOA->id_company,
                                'idUser' => $modelPOA ->id_profile,
                                'redirect' => Url::to(['/poa'])
                            ])
                        );
//                        Url::to(['/user/view' , 'id' => $modelPOA->id_profile]) .
//                        $modelPOA->getFIO()
//                        . ' (' . $modelPOA->id_profile . ')'
//                    ;
                }
            ],
            'job_post',
//            'url_form:ntext',
//            'url_upload_poa:ntext',
            // 'url_poa:ntext',
             'term_of_office',
            // 'checked',
            // 'STATUS_POA',
            // 'comments',
            [
                'attribute' => 'STATUS_POA',
                'label'=>'Статус',
                'format'=>'raw', // Возможные варианты: raw, html
                'filter' => \app\models\XprofileXcompany::getStatuses(),
                'value'=>function($data){
                    $id_user = Yii::$app->user->getId();
                    $modelPOA = $data;

                    if($modelPOA->STATUS_POA===XprofileXcompany::STATUS_POWER_OF_ATTORNEY_UNSIGNED) {
                        return
                            $modelPOA->getSTATUS_POWER_OF_ATTORNEY()
                            . ' '
//                            . \app\components\widgets\DownloadPOA::widget([
//                                'modelPOA' => $modelPOA,
//                                'return' => '/poa'
//                            ])
//                            . '<br><br>'
                            . \app\components\widgets\ButtonUpload::widget([
                                'model' => $modelPOA,
                                'typeDocument' => \app\models\XprofileXcompany::ClientPOA,
                                'ToggleButton' => ['label' => Html::img('/img/icons/upload-24.png'), 'class' => 'btn',[ 'data-toggle' => 'tooltip', 'title' => 'Загрузить']],
                                'completeRedirect' => Url::to(['/poa/index']),
                                'header' => 'Отправка подписанной доверенности',
                                'action' => \yii\helpers\Url::to(['/poa/upload-client-poa', 'idCompany' => $modelPOA->id_company, 'idUser' => $modelPOA->id_profile, 'completeRedirect' => '/poa'])
                            ])
                            . ' '
                            . \app\components\widgets\DownloadPOA::widget([
                                'modelPOA' => $modelPOA,
                                'return' => Url::to(['/poa/index'])
                            ])
                            ;
                    } else if ($modelPOA->STATUS_POA===XprofileXcompany::STATUS_POWER_OF_ATTORNEY_ON_CHECKING){
                        return
                            $modelPOA->getSTATUS_POWER_OF_ATTORNEY()
//                            .  Html::a(Html::img('/img/icons/delete-26.png'),
//                                Url::to(['/poa/delete-upload-poa', 'idCompany' => $modelPOA->id_company, 'idUser' => $modelPOA->id_profile ,'redirect' => '/poa']),
//                                [
//                                    'class' => 'btn',
//                                    'data-confirm' => 'Удалить загруженный скан?',
//                                    'data-toggle' => 'tooltip', 'title' => 'Удалить'])
                            . '<br>'
                            . \app\components\widgets\CheckPOA::widget([
                                'model' => $modelPOA,
                                'return' => '/poa'
                            ])
                        ;
                    } else if ($modelPOA->STATUS_POA===XprofileXcompany::STATUS_POWER_OF_ATTORNEY_FAILED){
                        return
                            $modelPOA->getSTATUS_POWER_OF_ATTORNEY()
                            . '<br>'
                            . ShowMessageWidget::widget(['helpMessage' => $modelPOA->comments])
                            . ' '
                            . \app\components\widgets\ButtonUpload::widget([
                                'model' => $modelPOA,
                                'typeDocument' => \app\models\XprofileXcompany::ClientPOA,
                                'ToggleButton' => ['label' => Html::img('/img/icons/upload-24.png'), 'class' => 'btn',[ 'data-toggle' => 'tooltip', 'title' => 'Загрузить']],
                                'completeRedirect' => '/company/index',
                                'header' => 'Отправка подписанной доверенности',
                                'action' => \yii\helpers\Url::to(['/poa/upload-client-poa',
                                    'idCompany' => $modelPOA->id_company,
                                    'idUser' => $modelPOA->id_profile,
                                    'completeRedirect' => '/poa'])

                            ]);
                    } else if ($modelPOA->STATUS_POA===XprofileXcompany::STATUS_POWER_OF_ATTORNEY_SIGNED ){
                        return
                            $modelPOA->getSTATUS_POWER_OF_ATTORNEY()
                            . ' '
                            . Html::a(Html::img('/img/icons/download-25.png'),[
                                Url::to(['/poa/download-confirm-poa', 'idCompany' => $modelPOA->id_company, 'idProfile' => $modelPOA->id_profile, 'return' => '/poa'])
                            ])
                            . '  '
                            . Html::a(Html::img('/img/icons/delete-26.png'),
                                Url::to(['/poa/delete-all-poa-files', 'idCompany' => $modelPOA->id_company, 'idProfile' => $modelPOA->id_profile, 'return' => '/poa']),
                                ['class' => 'btn',
                                    'data-confirm' => 'Удалить подписанную доверенность?',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Удалить'

                            ])
                            ;
                    }
                }
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
