<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use yii\helpers\Url;
use yii\web\JsExpression;


/* @var $this yii\web\View */
/* @var $model app\models\Payment */
/* @var $form yii\widgets\ActiveForm */
$label_user = ($model->direction == $model::CREDIT) ? 'Пользователь (получатель):': 'Пользователь (плательщик)';
$label_company = ($model->direction == $model::CREDIT) ? 'Юр. лицо (получатель):': 'Юр. лицо (плательщик)';
?>

<div class="payment-form">

    <?php $form = ActiveForm::begin([]); ?>

    <?= $form->field($model, 'direction')->radioList([1=>'Дебет',0=>'Кредит'], [
        'onchange' => '
            if($(this).find("input:checked").val() == 0){
                $("#label_user").html("Пользователь (получатель):");
                $("#label_company").html("Юр. лицо (получатель)");
            }
            if($(this).find("input:checked").val() == 1){
                $("#label_user").html("Пользователь (плательщик)");
                $("#label_company").html("Юр. лицо (плательщик)");            
            }
        '
    ])
    ?>
    <?= $form->field($model, 'type')->radioList(\app\models\TypePayment::getTypiesPaymentsArray(), [
        'encode' => false,
    ]) ?>
    <?= $form->field($model, 'cost')->input('tel') ?>

    <?=
        $form->field($model,'date')->widget(DatePicker::class, [
            'dateFormat' => 'dd.MM.yyyy'
        ]);

    ?>
    <label id="label_user"><?= $label_user?> </label>
    <?= \yii\jui\AutoComplete::widget([
            'clientOptions' => [
                'source' => $profiles,
                'autoFill' => true,
                'minLength' => '1',
                'select' => new JsExpression('function(event, ui) {               
                    $("#payer_user").val(ui.item.id);
                }')
            ],
            'options' => [
                'class' => 'form-control',
                'placeholder' => Yii::t('app', 'Номер телефона или ФИО')
            ]
    ])?>
<div id="company">
    <label id="label_company"><?= $label_company?> </label>
    <br>
    <?= \yii\jui\AutoComplete::widget([
        'clientOptions' => [
            'source' => $companies,
            'autoFill' => true,
            'select' => new \yii\web\JsExpression('function(event, ui){
                $("#payer_company").val(ui.item.id);
            }')
        ],
        'options' => [
            'class' => 'form-control',
            'placeholder' => Yii::t('app', 'Название организации или ИНН')
        ]
    ])?>
</div>
    <?= $form->field($model, 'id_user')->hiddenInput(['id' => 'payer_user'])->label(false) ?>

    <?= $form->field($model, 'id_implementer')->hiddenInput(['id' => 'recipient_user'])->label(false) ?>

    <?= $form->field($model, 'id_company')->hiddenInput(['id' => 'payer_company'])->label(false) ?>

    <?= $form->field($model, 'id_our_company')->hiddenInput(['id' => 'recipient_company'])->label(false) ?>

    <?= $form->field($model, 'status')->radioList([
        $model::STATUS_WAIT => 'В очереди', $model::STATUS_SUCCESS => 'Выполнен'
    ]) ?>

    <?= $form->field($model, 'comments')->textarea(['rows' => 6]) ?>


    <div class="form-group">
        <?= Html::submitButton('Провести', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
