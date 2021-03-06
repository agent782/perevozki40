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
        'pjaxSettings' => [
            'options' => [
                'id' => 'pjax_orders'
            ],
        ],
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
                'value' => function(Order $model){
                    return Html::a($model->id, '', [
                        'onclick' => "window.open ('"
                            . Url::toRoute(['/finance/order/view', 'id' => $model->id])
                            . "'); return false"
                    ]);
                    return ShowMessageWidget::widget([
                        'ToggleButton' => [
                            'label' => $model->id
                        ],
                        'helpMessage' => $model->getFullFinishInfo()                    ]);
                },
                'contentOptions' => [
                    'style' => 'font-size: 12px'
                ]
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
                                'redirect' => Url::to()
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
                                'redirect' => Url::to()
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
                                'redirect' => Url::to()
                            ]),
                            'index' => $index
                        ])
                    ;
                    return $return;
                },
                'filter' => true
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
                                'redirect' => Url::to()
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
                                'redirect' => Url::to()
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
                                'redirect' => Url::to()
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
                    return
                        $model->cost_finish
                        . ' / '
                        . $model->cost_finish_vehicle;
                },
                'contentOptions' => [
                    'style' => 'font-size: 14px'
                ]
            ],
            [
                'class' => \kartik\grid\EditableColumn::class,
                'attribute' => 'date_paid',
                'label' => 'Дата оплаты',
                'editableOptions' => [
                    'inputType' => \kartik\editable\Editable::INPUT_DATE,
                    'formOptions' => [
                        'action' => \yii\helpers\Url::to([ '/finance/order/changeDatePaid' ])
                    ],
                    'options'=>[
                        'type' => \kartik\date\DatePicker::TYPE_INLINE,
                        'pluginOptions'=>[
                            'format' => 'dd.mm.yyyy',
                            'autoclose' => true,
                            'todayBtn' => true,
                            'todayHighlight' => true,
                        ]
                    ]
                ],
            ],
            [
                'class' => \kartik\grid\EditableColumn::class,
                'attribute' => 'paid_status',
                'label' => 'Оплата',
                'value' => 'paidText',
                'filter' => Html::activeCheckboxList($searchModel, 'paid_status', $searchModel->getArrayPaidStatuses()),
                'editableOptions' => [
                    'inputType' => \kartik\editable\Editable::INPUT_RADIO_LIST,
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
                'class' => \kartik\grid\EditableColumn::class,
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
                'editableOptions' => [
                    'inputType' => \kartik\editable\Editable::INPUT_RADIO_LIST,
                    'data' => ArrayHelper::map(\app\models\TypePayment::find()->all(), 'id', 'min_text')
                    ,
                    'formOptions' => [
                        'action' => \yii\helpers\Url::to([ '/finance/order/changePaymentType' ])
                    ]
                ],
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
                    $return .= ' ' . Html::a($model->clientInfo, Url::to(['/finance/profile/view', 'id' => $profile->id_user]));
                    $return .= ShowMessageWidget::widget([
                        'helpMessage' => $profile->getProfileInfo(true,true,true,true),
                        'header' => $profile->fioFull,
                        'ToggleButton' => ['label' => Html::icon('info-sign'), 'style' => 'cursor: help']
                    ]);
                    return $return;
                }
            ],
//            [
//                'label' => 'Водитель',
//                'format' => 'raw',
//                'value' => function(Order $model){
//                    $return = '';
//                    $profile = $model->carOwner;
//                    if($profile)
//                        $return  .= Html::a($profile->fioFull, Url::to(['/finance/profile/view', 'id' => $profile->id_user]));
//                    $return .= ShowMessageWidget::widget([
//                        'helpMessage' => $model->vehicle->getFullInfo(),
//                        'ToggleButton' => ['label' => Html::icon('info-sign'), 'style' => 'cursor: help']
//                    ]);
//                    return $return;
//                }
//            ],
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
            [
                'label' => '',
                'format' => 'raw',
                'filter' =>
                    Html::activeCheckbox($searchModel, 'hasInvoiceOrCertificate',
                        ['label' => ' Только не выставленные счета или акты']
                    )
                ,
                'filterOptions' => ['class' => 'minRoute'],
            ],

//            ['class' => 'yii\grid\ActionColumn'],
        ],

    ]); ?>
</div>
