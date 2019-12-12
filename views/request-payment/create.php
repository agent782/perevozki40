<?php

use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use app\models\Payment;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\RequestPayment */

$this->title = 'Запрос на выплату';

?>
<div class="container request-payment-create">
    <a href="<?= Url::to('/request-payment/index')?>">История выплат</a>
    <br>
    <h3><?= Html::encode($this->title)?></h3>

    <p>Возможная сумма для получения - <?= ($max_cost>0)?$max_cost:0 ?> р.</p>
    <p>Минимальная сумма для получения - <?= $min_cost ?> р.</p>

    <?php
        $form = ActiveForm::begin();
    ?>
    <?= $form->field($model, 'type_payment')->dropDownList([
        Payment::TYPE_SBERBANK_CARD => 'На банковскую карту',
        Payment::TYPE_BANK_TRANSFER => 'На р/с ИП или ООО'
    ], ['id' => 'type_payment', 'style' => 'width: auto'])?>
    <label id="label_requisites">Номер телефона привязанный к картe Сбербанк или номер банковской карты и ФИО владельца</label>
    <?= $form->field($model, 'requisites')->textarea(['id' => 'requisites'])->label(false)?>
    <?= $form->field($model, 'file')->fileInput(['id' => 'file', 'disabled' => true])?>
    <?= $form->field($model, 'cost')->input('tel')?>
    <?= Html::submitButton('Отправить запрос', ['class' => 'btn btn-primary'])?>
    <?php
        $form::end();
    ?>

</div>

<script>
    $(document).ready(function () {
        if($('#type_payment').val() === '2'){
            $('#label_requisites').text('Номер телефона привязанный к картe Сбербанк или номер банковской карты и ФИО владельца');
            $('#file').attr('disabled', true);
            $('#file').val('');
        }
        if($('#type_payment').val() === '3'){
            $('#label_requisites').text('Название организации или ИП');
            $('#file').attr('disabled', false);
        }
        $('#type_payment').on('change', function () {
            if($('#type_payment').val() === '2'){
                $('#label_requisites').text('Номер телефона привязанный к картe Сбербанк или номер банковской карты и ФИО владельца');
                $('#file').attr('disabled', true);
                $('#file').val('');
            }
            if($('#type_payment').val() === '3'){
                $('#label_requisites').text('Название организации или ИП');
                $('#file').attr('disabled', false);
            }
        });
    });
</script>
