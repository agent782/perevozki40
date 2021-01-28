<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\Pjax;


/* @var $this yii\web\View */
/* @var $model app\models\Payment */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="payment-form">

    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
        'validationUrl' => 'validate-payment'
    ]); ?>

    <?= $form->field($model, 'direction')->radioList([1=>'Дебет',0=>'Кредит'], [
//        'onchange' => '
//            if($(this).find("input:checked").val() == 0){
//                $("#label_user").html("Пользователь (получатель):");
//                $("#label_company").html("Юр. лицо (получатель)");
//            }
//            if($(this).find("input:checked").val() == 1){
//                $("#label_user").html("Пользователь (плательщик)");
//                $("#label_company").html("Юр. лицо (плательщик)");
//            }
//        '
    ])
    ?>
    <?= $form->field($model,'calculation_with')->radioList($model->getArrayCalculationWith())?>
    <?= $form->field($model, 'type')->radioList(\app\models\TypePayment::getTypiesPaymentsArray(), [
        'encode' => false,
    ]) ?>
    <?= $form->field($model, 'cost')->input('tel') ?>

    <?=
        $form->field($model,'date')->widget(DatePicker::class, [
            'dateFormat' => 'dd.MM.yyyy'
        ]);

    ?>
    <?= $form->field($model, 'id_user')->hiddenInput(['id' => 'payer_user', 'readonly' => true]) ?>

    <?= \yii\jui\AutoComplete::widget([
            'id' => 'form',
            'clientOptions' => [
                'source' => $profiles,
                'autoFill' => true,
                'minLength' => '1',
                'select' => new JsExpression('function(event, ui) {               
                    $("#payer_user").val(ui.item.id);
                }'),
                'change' => new JsExpression('function(event, ui) {               
                    if(!ui.item) {
                        $("#payer_user").val("");
                    }
                }'),
            ],
            'options' => [
                'class' => 'form-control',
                'placeholder' => Yii::t('app', 'Номер телефона или ФИО')
            ]
    ])?>

    <div id="company">
        <?= $form->field($model, 'id_company')->hiddenInput(['id' => 'payer_company'])?>

    <?= \yii\jui\AutoComplete::widget([
        'clientOptions' => [
            'source' => $companies,
            'autoFill' => true,
            'select' => new \yii\web\JsExpression('function(event, ui){
                $("#payer_company").val(ui.item.id);
                $.pjax.reload({
                        url : "/finance/payment/create",
                        container: "#chkboxlist-invoices",
//                        dataType:"json",
                        type: "POST", 
                        data: {  
                              "id_company" : ui.item.id,
                              "form_id" : "' . $form->id . '",
//                              "model" : "' . serialize($model) . '"
                         }
                    })
            }'),
            'change' => new JsExpression('function(event, ui) {               
                    if(!ui.item) {
                        $("#payer_company").val("");
                    }
                }'),
        ],
        'options' => [
            'class' => 'form-control',
            'placeholder' => Yii::t('app', 'Название организации или ИНН')
        ]
    ])?>

    </div>
    <?= $form->field($model, 'id_implementer')->hiddenInput(['id' => 'implementer'])->label(false) ?>

    <?php
        $pjax = Pjax::begin(['id' => 'chkboxlist-invoices']);
    ?>

    <?php
        $pjax::end();
    ?>

    <?= $form->field($model, 'id_our_company')->dropDownList($our_companies)?>

    <?= $form->field($model, 'status')->radioList([
        $model::STATUS_WAIT => 'В очереди', $model::STATUS_SUCCESS => 'Выполнен'
    ]) ?>

    <?= $form->field($model, 'comments')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success', 'name' => 'button', 'value' => 'select']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
