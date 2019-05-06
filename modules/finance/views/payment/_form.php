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
$label_company = ($model->direction == $model::CREDIT) ? 'Юр. лицо (получатель):': 'Юрю лицо (плательщик)';
?>

<div class="payment-form container">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'direction')->radioList([1=>'Дебет',0=>'Кредит'], [
        'onchange' => '
            alert();            
        '
    ])
    ?>

    <?= $form->field($model, 'cost')->input('tel') ?>

    <?= $form->field($model, 'type')->radioList(\app\models\TypePayment::getTypiesPaymentsArray(), ['encode' => false]) ?>
    <?= $form->field($model, 'date')->widget(DatePicker::class,[
        'dateFormat' => 'php: d.m.Y',
        'value' => date('d.m.Y')
    ]) ?>

    <label><?= $label_user?> </label>
    <?= \yii\jui\AutoComplete::widget([
            'clientOptions' => [
                'source' => Url::to(['/user/autocomplete']),
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

    <label><?= $label_company?> </label>
    <?= \yii\jui\AutoComplete::widget([
        'clientOptions' => [
            'source' => \app\models\Company::find()->select(['name as value', 'name_full as label', 'id as id' ])->asArray()->all(),
            'autoFill' => true,
            'select' => new \yii\web\JsExpression('function(event, ui){
                $("#payer_company").val(ui.item.id);
            }')
        ]
    ])?>

    <?= $form->field($model, 'id_payer_user')->hiddenInput(['id' => 'payer_user'])->label(false) ?>

    <?= $form->field($model, 'id_recipient_user')->hiddenInput(['id' => 'recipient_user'])->label(false) ?>

    <?= $form->field($model, 'id_payer_company')->hiddenInput(['id' => 'payer_company'])->label(false) ?>

    <?= $form->field($model, 'id_recipient_company')->hiddenInput(['id' => 'recipient_company'])->label(false) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'comments')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'sys_info')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'create_at')->textInput() ?>

    <?= $form->field($model, 'update_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
