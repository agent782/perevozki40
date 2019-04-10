<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\jui\AutoComplete;
use yii\helpers\Url;
use yii\web\JsExpression;
use app\components\widgets\AddCompany;
use yii\widgets\Pjax;
use app\models\Payment;

/* @var $user \app\models\User */

$this->title = 'Поиск ТС';
?>

<div class="find-vehicle">

    <h3><?= Html::encode($this->title) ?></h3>
    <label>Поиск ТС:</label>
    <?=
    AutoComplete::widget([
        'clientOptions' => [
            'source' => Url::to(['/logist/order/autocomplete']),
            'autoFill' => true,
            'minLength' => '0',
            'select' => new JsExpression('function(event, ui) {               
               $("#label").html("Клиент");
               $("#id").val(ui.item.id);
//               alert($(this).val());
               $("#username").val(ui.item.phone);
               $("#phone2").val(ui.item.phone2);
               $("#email").val(ui.item.email);
               $("#email2").val(ui.item.email2);
               $("#name").val(ui.item.name);
               $("#surname").val(ui.item.surname);
               $("#patrinimic").val(ui.item.patrinimic);
               
//               $.pjax.reload({
//                          url : "/logist/order/pjax-add-company",
//                          container: "#companies",
////                          dataType:"json",
//                          type: "POST", 
//                        data: {  
//                              "id_user" : ui.item.id 
//                         }                       
//                       });
            }'),
            'response' => new JsExpression('function(event, ui) {
               $("#username").val($(this).val());
            }'),
            'change' => new JsExpression('function(event, ui) {
                if(!ui.item) {
                     $("#label").html("Новый влыделец ТС");

                }
            }'),
        ],
        'options' => [
            'id' => 'search',
            'class' => 'form-control',
            'placeholder' => Yii::t('app', 'Введите номер телефона')
        ]
    ])
    ?>
    <br><br>
    <label id="label-form">Новый владелец ТС</label>
    <?php
        $form = ActiveForm::begin();
    ?>
    <div class="col-lg-4">
        <?= $form->field($user, 'id')->hiddenInput(['id' => 'id_user'])->label(false)?>
        <?= $form->field($profile, 'surname')->input('text',  ['id' => 'surname'])?>
        <?= $form->field($profile, 'name')->input('text',  ['id' => 'name'])?>
        <?= $form->field($profile, 'patrinimic')->input('text',  ['id' => 'patrinimic'])?>
        <?= $form->field($user, 'username')->input('tel',  ['id' => 'username', 'readonly' => true])?>
        <?= $form->field($user, 'email')->input('email',  ['id' => 'email'])?>
        <?= $form->field($profile, 'phone2')->input('tel',  ['id' => 'phone2'])?>
        <?= $form->field($profile, 'email2')->input('email',  ['id' => 'email2'])?>
    </div>
    <div class="col-lg-8" id="info">

    </div>
    <?php
        $form::end();
    ?>

</div>