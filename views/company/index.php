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
/* @var $this yii\web\View */
/* @var $searchModel app\models\CompanySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Юридические лица';
$this->params['breadcrumbs'][] = $this->title;





?>
<div class="company-index">


    <h3><?= Html::encode($this->title) ?></h3>

    <p>
        <?= Html::a('Добавить организацию или ИП', ['create'], ['class' => 'btn btn-success'])
        . '<br>' .Yii::$app->session->getFlash('maxCompanies') . Yii::$app->session->getFlash('errorCreatePOA') ?>
    </p>

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
                        return '1. Скачайте заполненный бланк.<br>
                                2. Распечатайте, подпишите и заверьте печатью.<br>
                                3. Отсканируйте и отправьте Договор на проверку.<br><br>'
                            . Html::a('Скачать бланк',
                                \yii\helpers\Url::to(['download-document', 'idCompany' => $data->id, 'type' => \app\models\Document::TYPE_CONTRACT_CLIENT]),
                                ['class'=>'btn btn-info', 'data-toggle' => 'tooltip', 'title' => 'Инфорсация'])
                            . '<br><br>'
                            . \app\components\widgets\ButtonUpload::widget([
                                    'model' => $modelDocument,
                                    'typeDocument' => \app\models\Document::TYPE_CONTRACT_CLIENT,
                                    'completeRedirect' => '/company/index'
                            ])
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
                            . Html::a('Скачать бланк',
                                \yii\helpers\Url::to(['download-document', 'idCompany' => $data->id, 'type' => \app\models\Document::TYPE_CONTRACT_CLIENT]),
                                ['class'=>'btn btn-info', 'data-toggle' => 'tooltip', 'title' => 'Инфорсация'])
                            . '<br><br>'
                            . \app\components\widgets\ButtonUpload::widget([
                                'model' => $modelDocument,
                                'typeDocument' => \app\models\Document::TYPE_CONTRACT_CLIENT,
                                'completeRedirect' => '/company/index'
                            ])
                            ;
                    }
                    else {
                        return $modelDocument->getStatusString(). ' '
                                . Html::a('Скачать бланк',
                                \yii\helpers\Url::to(['download-document', 'idCompany' => $data->id, 'type' => \app\models\Document::TYPE_CONTRACT_CLIENT]),
                                ['class'=>'btn btn-info'])
                                . '<br><br>'
                                . \app\components\widgets\ButtonUpload::widget(['model' => $modelDocument, 'typeDocument' => \app\models\Document::TYPE_CONTRACT_CLIENT])
                                ;

                    }

                },
//                'filter' => Category::getParentsList()
            ],
            [
                'label'=>'Доверенность',
                'format'=>'raw', // Возможные варианты: raw, html
                'value'=>function($data){
                    $id_user = Yii::$app->user->getId();
                    $XprofileXcompany = \app\models\XprofileXcompany::find()
                        ->where([
                           'id_company' => $data->id,
                            'id_profile' => $id_user,
                        ])
                        ->one();
                    if(!$XprofileXcompany->url_power_of_attorney)
                        return
                            $XprofileXcompany->getSTATUS_POWER_OF_ATTORNEY()
                            . ' '
                            . Html::a('Скачать бланк',
                                Url::to(['/poa/download-form', 'id_company' => $data->id, 'id_profile'=>$id_user, 'return'=>'/company/index']),
                                ['class'=>'btn btn-info', 'data-toggle' => 'tooltip', 'title' => 'Инфорсация']
                            )
                            . '<br><br>'

                        ;

                }
            ],
//            'xprofileXcompany.sTATUS_POWER_OF_ATTORNEY',
//            'address',
//            'address_real',
            // 'address_post',
            // 'value',
            // 'address_value',
            // 'branch_type',
            // 'capital',
            // 'email:email',
            // 'email2:email',
            // 'email3:email',
            // 'kpp',
            // 'management_name',
            // 'management_post',
            // 'name_full',
            // 'name_short',
            // 'ogrn',
            // 'ogrn_date',
            // 'okpo',
            // 'okved',
            // 'opf_short',
            // 'phone',
            // 'phone2',
            // 'phone3',
            // 'citizenship',
            // 'state_actuality_date',
            // 'state_registration_date',
            // 'state_liquidation_date',
            // 'state_status',
            // 'data_type',
            // 'status',
            // 'raiting',
            // 'created_at',
            // 'updated_at',
            // 'FIO_contract',
            // 'basis_contract',
            // 'job_contract',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>



    <?php
//        $modelDocument = \app\models\Document::findOne(['id_company' => $data->id, 'type' => \app\models\Document::TYPE_CONTRACT_CLIENT]);
//        var_dump($modelDocument);
//        $form = ActiveForm::begin();
    ?>

</div>
