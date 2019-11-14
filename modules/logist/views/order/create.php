<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\jui\AutoComplete;
use yii\helpers\Url;
use yii\web\JsExpression;
use app\components\widgets\AddCompany;
use yii\widgets\Pjax;
use app\models\Payment;

/* @var $this yii\web\View */
/* @var $modelOrder app\models\Order */
/* @var $TypiesPayment array */
/* @var $PhonesFIOList array */
/* @var $user \app\models\User */
/* @var $profile \app\models\Profile*/

$this->title = 'Оформлление заказа';
//<<<<<<< HEAD
//var_dump($modelOrder->type_payment);
//=======
//>>>>>>> a155b654d1118940b36589aaae156cebe3265c0d
?>

<div class="container order-create">
    <input id="role" hidden value="<?= Yii::$app->user->identity->getRole()?>">
    <h3><?= Html::encode($this->title) ?></h3>
    <label>Поиск клиента:</label>
    <?=
    AutoComplete::widget([
        'clientOptions' => [
//            'source' => Url::to(['/logist/order/autocomplete']),
            'source' => \app\models\Profile::getArrayForAutoComplete(false),
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
               var id = ui.item.id;
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
                     $("#label").html("Новый клиент");
                       $("#id").val("");
//                       $("#username").val("");
                       $("#phone2").val("");
                       $("#email").val("");
                       $("#email2").val("");
                       $("#name").val("");
                       $("#surname").val("");
                       $("#patrinimic").val("");
                       $("#surname").focus();
                        
                }
            }'),
        ],
        'options' => [
            'id' => 'search',
            'class' => 'form-control',
            'placeholder' => Yii::t('app', 'Введите номер телефона'),
        ]
    ])
    ?>
    <br><br>
    <h2 id="label">Новый клиент</h2>
    <?php $formFinishOrder = ActiveForm::begin([
        'action' => '/order/create',
//        'enableAjaxValidation' => true,
//        'validationUrl' => \yii\helpers\Url::to(['/user/validate-user']),
        'fieldConfig' => [
            'labelOptions' => ['class' => 'col-lg-12 control-label'],
        ],
    ]);?>
    <input id="role" value="<?= Yii::$app->user->identity->getRole()?>" hidden></input>
    <div class="col-lg-4">
        <?= $formFinishOrder->field($user, 'id')->hiddenInput(['id' => 'id_user'])->label(false)?>
        <?= $formFinishOrder->field($profile, 'name')->input('text',  ['id' => 'name'])?>
        <?= $formFinishOrder->field($profile, 'surname')->input('text',  ['id' => 'surname'])?>
        <?= $formFinishOrder->field($profile, 'patrinimic')->input('text',  ['id' => 'patrinimic'])?>
    </div>
    <div class="col-lg-4">
        <?= $formFinishOrder->field($user, 'username')->input('tel',  ['id' => 'username', 'readonly' => true])?>
        <?= $formFinishOrder->field($user, 'email')->input('email',  ['id' => 'email'])?>
        <?= $formFinishOrder->field($profile, 'phone2')->input('tel',  ['id' => 'phone2'])?>
        <?= $formFinishOrder->field($profile, 'email2')->input('email',  ['id' => 'email2'])?>
    </div>

    <div class="col-lg-12">
    <?php
        echo Html::submitButton(
                'Оформить заказ',
                ['class' => 'btn btn-success', 'name' => 'button', 'value' => 'logist_add_company']);
    ?>
    </div>
    <?php $formFinishOrder::end()?>
</div>

