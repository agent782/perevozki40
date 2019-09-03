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

$this->title = 'Поиск пользователя';
?>

<div class="find-user">

    <h3><?= Html::encode($this->title) ?></h3>
    <?=
    AutoComplete::widget([
        'clientOptions' => [
//            'source' => Url::to(['/user/autocomplete']),
            'source' => \app\models\Profile::getArrayForAutoComplete(false),
            'autoFill' => true,
            'minLength' => '0',
            'select' => new JsExpression('function(event, ui) {               
               $("#label").html("Пользователь");
               $("#id").val(ui.item.id);
//               alert($(this).val());
               $("#username").val(ui.item.phone);
               $("#phone2").val(ui.item.phone2);
               $("#email").val(ui.item.email);
               $("#email2").val(ui.item.email2);
               $("#sex :radio[value =" + ui.item.sex +"]").attr("checked", true);
               $("#is_driver :radio[value =" + ui.item.is_driver +"]").attr("checked", true);
               $("#name").val(ui.item.name);
               $("#surname").val(ui.item.surname);
               $("#patrinimic").val(ui.item.patrinimic);
               $("#info").html(ui.item.info);
               
//               $.pjax.reload({
//                          url : "/logist/order/pjax-info-vehicle",
//                          container: "#info",
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
                     $("#label").html("Новый пользователь");
                     $("#id").val("");
//                       $("#username").val("");
                       $("#phone2").val("");
                       $("#email").val("");
                       $("#email2").val("");
                       $("#name").val("");
                       $("#surname").val("");
                       $("#patrinimic").val("");
                       $("#info").html("");
                       $("#surname").focus();
                }
            }'),
        ],
        'options' => [
            'id' => 'search',
            'class' => 'form-control',
            'placeholder' => Yii::t('app', 'Введите номер телефона, ФИО или ID')
        ]
    ])
    ?>
    <br>

    <?php
        $form = ActiveForm::begin();
    ?>
    <div class="col-lg-12">
    </div>
    <br><br><br>
    <label id="label">Новый пользователь</label>
    <br>
    <div class="col-lg-4">
        <?= $form->field($user, 'id')->hiddenInput(['id' => 'id_user'])->label(false)?>
        <?= $form->field($profile, 'surname')->input('text',  ['id' => 'surname'])?>
        <?= $form->field($profile, 'name')->input('text',  ['id' => 'name'])?>
        <?= $form->field($profile, 'patrinimic')->input('text',  ['id' => 'patrinimic'])?>
        <?= $form->field($profile, 'sex')->radioList(['Мужской', 'Женский'], ['id' => 'sex'])?>
        <?= $form->field($user, 'username')->input('tel',  ['id' => 'username', 'readonly' => true])?>
        <?= $form->field($user, 'email')->input('email',  ['id' => 'email'])?>
        <?= $form->field($profile, 'phone2')->input('tel',  ['id' => 'phone2'])?>
        <?= $form->field($profile, 'email2')->input('email',  ['id' => 'email2'])?>
        <?= $form->field($profile, 'is_driver')->radioList(['Нет', 'Да'], ['id' => 'is_driver'])->label('Водитель')?>
        <?= $form->field($user, 'old_id')?>
    </div>
    <div class="col-lg-8" id="info">

    </div>
    <div class="col-lg-12">
        <?= Html::submitButton('Далее', ['class' => 'btn btn-success'])?>
    </div>
    <?php
        $form::end();
    ?>
    <br>

</div>