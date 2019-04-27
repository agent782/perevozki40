<?php

use yii\bootstrap\Html;
use kartik\grid\GridView;
//use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\components\widgets\UploadInvoiceWidget;
use app\components\widgets\ShowMessageWidget;
use yii\helpers\Url;
use app\models\Invoice;
/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $modelOrder \app\models\Order */

$this->title = 'Журнал заказов';
?>
<div class="order-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
//        'pjax' => true,
        'options' => [
            'class' => 'minRoute container',
        ],
        'columns' => [
            [
                'label' => 'Счет',
                'attribute' => 'invoiceNumber',
                'format' => 'raw',
                'value' => function($model, $value, $index) {
                    if($model->type_payment !== \app\models\Payment::TYPE_BANK_TRANSFER) return;
                    $return = ($modelInvoice = $model->invoice)
                        ? Html::a(
                            '<b style = "font-size: 18px">' . $modelInvoice->number . '</b> от ' . $modelInvoice->date,
                            Url::to(['/finance/invoice/download',
                                'pathToFile' => Yii::getAlias('@invoices/') . $modelInvoice->url,
                                'redirect' => '/finance/order'
                            ]),
                            ['title' => 'Скачать']
                        ). ' '
                        . UploadInvoiceWidget::widget([
                            'modelInvoice' => $modelInvoice,
                            'ToggleButton' => [
                                'label' => Html::icon('edit')],
                            'fieldFileLable' => 'Счет:',
                            'id_order' => 'value',
                            'type_document' => Invoice::TYPE_INVOICE,
                            'action' => Url::to(['/finance/invoice/upload',
                                'type' => Invoice::TYPE_INVOICE,
                                'id_order' => $value,
                                'redirect' => '/finance/order'
                            ]),
                            'index' => $index
                        ])
                        : UploadInvoiceWidget::widget([
                            'modelInvoice' => new Invoice(),
                            'ToggleButton' => [
                                'label' => Html::icon('upload', ['style' => 'font-size: large;'])],
                            'fieldFileLable' => 'Счет:',
                            'id_order' => 'value',
                            'type_document' => Invoice::TYPE_INVOICE,
                            'action' => Url::to(['/finance/invoice/upload',
                                'type' => Invoice::TYPE_INVOICE,
                                'id_order' => $value,
                                'redirect' => '/finance/order'
                            ]),
                            'index' => $index
                        ])
                    ;
                    return $return;
                }
            ],
            [
                'label' => 'Акт',
                'attribute' => 'certificateNumber',
                'format' => 'raw',
                'value' => function($model, $value, $index) {
                    if($model->type_payment !== \app\models\Payment::TYPE_BANK_TRANSFER) return;
                    $return = ($modelCertificate = $model->certificate)
                        ? Html::a(
                            '<b style = "font-size: 14px">' . $modelCertificate->number . '</b> от ' . $modelCertificate->date,
                            Url::to(['/finance/invoice/download',
                                'pathToFile' => Yii::getAlias('@certificates/') . $modelCertificate->url,
                                'redirect' => '/finance/order'
                            ]),
                            ['title' => 'Скачать']
                        ) . ' '
                        . UploadInvoiceWidget::widget([
                            'modelInvoice' => $modelCertificate,
                            'ToggleButton' => [
                                'label' => Html::icon('edit')],
                            'fieldFileLable' => 'Акт:',
                            'id_order' => 'value',
                            'type_document' => Invoice::TYPE_CERTIFICATE,
                            'action' => Url::to(['/finance/invoice/upload',
                                'type' => Invoice::TYPE_CERTIFICATE,
                                'id_order' => $value,
                                'redirect' => '/finance/order'
                            ]),
                            'index' => $index
                        ])
                        : UploadInvoiceWidget::widget([
                            'modelInvoice' => new Invoice(),
                            'ToggleButton' => [
                                'label' => Html::icon('upload', ['style' => 'font-size: large;'])],
                            'fieldFileLable' => 'Акт:',
                            'id_order' => 'value',
                            'type_document' => Invoice::TYPE_CERTIFICATE,
                            'action' => Url::to(['/finance/invoice/upload',
                                'type' => Invoice::TYPE_CERTIFICATE,
                                'id_order' => $value,
                                'redirect' => '/finance/order'
                            ]),
                            'index' => $index
                        ])
                    ;
                    return $return;
                },
            ],
            [
                'label' => 'Сумма',
                'format' => 'raw',
                'value' => function($model){
                    return $model->cost_finish . ' / ' . $model->cost_finish_vehicle;
                },
            ],
            [
                'attribute' => 'paid_status',
                'label' => 'Оплата',
                'format' => 'raw',
                'value' => function($model){
                    return $model->paidText;
                }
            ],
            [
                'attribute' =>'type_payment',
                'format' => 'raw',
                'value' => function($model){
                    return $model->paymentMinText;
                },
                'filter' =>
                    Html::activeCheckboxList($searchModel, 'type_payments',
                    ArrayHelper::map(\app\models\TypePayment::find()->all(), 'id', 'min_text')
                    )
                    ,
                'filterOptions' => ['class' => 'minRoute']

            ],
            [
//                'attribute' => 'company.companyInfo',
                'label' => 'Плательщик',
                'format' => 'raw',
                'value' => function($model, $index, $value){
                    $company = $model->company;
                    if (!$company) return null;
                    return Html::a($company->name, Url::to(['/finance/company/view', 'id' => $company->id]));
                },
                'filter' => \kartik\select2\Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'id_company',
                    'data' => $companies,
                    'pluginOptions' => [
                        'allowClear' => true,
                        'change' => 'alert();'
                    ],
                ])
//                'attribute' => 'company.companyInfo'
            ],
            [
                'label' => 'Заказчик',
                'format' => 'raw',
                'value' => function($model){
                    $profile = $model->profile;
                    return Html::a($profile->fioFull, Url::to(['/finance/profile/view', 'id' => $profile->id_user]));
                }
            ],
            [
                'attribute' => 'id',
                'label' => '№ заказа',
                'format' => 'raw',
                'value' => function($model){
                    return Html::a($model->id, Url::to(['/finance/order/view', 'id' => $model->id]));
                }
            ],
            'datetime_finish',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
