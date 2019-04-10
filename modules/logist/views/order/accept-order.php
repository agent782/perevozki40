<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\jui\AutoComplete;
use yii\helpers\Url;
use yii\web\JsExpression;
use app\components\widgets\AddCompany;
use yii\widgets\Pjax;
use app\models\Payment;


/* @var $modelOrder app\models\Order */


$this->title = 'Оформлление заказа';
var_dump($modelOrder->type_payment);
?>

<div class="order-create">

    <h3><?= Html::encode($this->title) ?></h3>
    <label>Поиск клиента:</label>
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
            'placeholder' => Yii::t('app', 'Введите номер телефона')
        ]
    ])
    ?>
    <br><br>