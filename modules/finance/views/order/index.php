<?php

use yii\bootstrap\Html;
use kartik\grid\GridView;
//use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\components\widgets\UploadInvoiceWidget;
use app\components\widgets\ShowMessageWidget;
use yii\helpers\Url;
use app\models\Invoice;
use app\models\Order;
/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $modelOrder \app\models\Order */

$this->title = 'Журнал заказов';
?>
<div class="order-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pjax' => true,
        'responsiveWrap' => false,
//        'responsive' => true,
//        'floatHeader' => true,
//        'pjaxSettings' => [
//            'options' => ['id' => 'grid-orders']
//        ],
        'options' => [
            'class' => 'minRoute container',
        ],
        'columns' => [
            [
                'attribute' => 'id',
                'label' => '№ заказа',
                'format' => 'raw',
                'value' => function($model){
                    return Html::a($model->id, Url::to(['/finance/order/view', 'id' => $model->id]));
                }
            ],
            'datetime_finish',
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
                            ['title' => 'Скачать', 'data-pjax' => "0"]
                        ). ' '
                        . UploadInvoiceWidget::widget([
                            'modelInvoice' => $modelInvoice,
                            'ToggleButton' => [
                                'label' => Html::icon('edit')],
                            'fieldFileLable' => 'Счет:',
                            'id_order' => $value,
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
                            'id_order' => $value,
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
                            ['title' => 'Скачать', 'data-pjax' => "0"]
                        ) . ' '
                        . UploadInvoiceWidget::widget([
                            'modelInvoice' => $modelCertificate,
                            'ToggleButton' => [
                                'label' => Html::icon('edit')],
                            'fieldFileLable' => 'Акт:',
                            'id_order' => $value,
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
                            'id_order' => $value,
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
                'class' => \kartik\grid\EditableColumn::class,
                'attribute' => 'paid_status',
                'label' => 'Оплата',
                'value' => 'paidText',
                'filter' => Html::activeCheckboxList($searchModel, 'paid_status', $searchModel->getArrayPaidStatuses()),
                'editableOptions' => [
                    'inputType' => \kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                    'data' => $searchModel->getArrayPaidStatuses(),
                    'formOptions' => [
                        'action' => \yii\helpers\Url::to([ '/finance/order/pru' ])
                    ]
                ],
            ],
            [
                'class' => \kartik\grid\EditableColumn::class,
                'attribute' => 'avans_client',
                'editableOptions' => [
                    'inputType' => \kartik\editable\Editable::INPUT_TEXT,
                    'formOptions' => [
                        'action' => \yii\helpers\Url::to([ '/finance/order/set_avans_client' ])
                    ]
                ],
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
                'filterOptions' => ['class' => 'minRoute'],
            ],
            [
//                'attribute' => 'company.companyInfo',
                'label' => 'Плательщик',
                'attribute' => 'companyName',
                'format' => 'raw',
                'value' => function($model, $index, $value){
                    $company = $model->company;
                    if (!$company) $return = null;
                    else $return = Html::a($company->name, Url::to(['/finance/company/view', 'id' => $company->id]));
                    $return .= ' ' . Html::a(Html::icon('edit', ['title' => 'Добавить юр. лицо', 'class' => 'btn-xs btn-primary']),
                            ['/logist/order/add-company', 'id_order' => $model->id, 'redirect' => '/finance/order']);
                    return $return;
                }
//                'filter' => \yii\jui\AutoComplete::widget([
//                    'model' => $searchModel,
//                    'attribute' => 'companyName',
//                    'clientOptions' => [
//                        'source' => \app\models\Company::find()->select(['name_full as value', 'name_full as label'])->asArray()->all(),
//                        'autoFill' => true,
//                    ]
//                ])
            ],
            [
                'label' => 'Заказчик',
                'format' => 'raw',
                'value' => function(Order $model){
                    $profile = $model->profile;
                    $return = '';
                    if($model->re && $model->id_user == $model->id_car_owner){
                        $return .= $model->comment;
                    }
                    $return .= ' ' . Html::a($profile->fioFull, Url::to(['/finance/profile/view', 'id' => $profile->id_user]));
                    return $return;
                }
            ],
            [
                'label' => 'Водитель',
                'format' => 'raw',
                'value' => function($model){
                    $profile = $model->carOwner;
                    if($profile)
                        return Html::a($profile->fioFull, Url::to(['/finance/profile/view', 'id' => $profile->id_user]));
                }
            ],
//            [
//                'class' => \kartik\grid\EditableColumn::class,
//                'attribute' => 'paid_car_owner_status',
//                'value' => 'paidCarOwnerText',
//                'filter' => Html::activeCheckboxList($searchModel, 'paid_car_owner_status', $searchModel->getArrayPaidStatuses()),
//                'editableOptions' => [
//                    'inputType' => \kartik\editable\Editable::INPUT_DROPDOWN_LIST,
//                    'data' => $searchModel->getArrayPaidStatuses(),
//                    'formOptions' => [
//                        'action' => \yii\helpers\Url::to([ '/finance/order/changePaidCarOwnerStatus' ])
//                    ]
//                ],
//            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
