<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\jui\AutoComplete;
use yii\helpers\Url;
use yii\web\JsExpression;
use app\components\widgets\AddCompanyWidget;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $modelOrder app\models\Order */
/* @var $TypiesPayment array */
/* @var $PhonesFIOList array */
/* @var $user \app\models\User */
/* @var $profile \app\models\Profile*/

$this->title = 'Оформлление заказа';
//var_dump($route);
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
               var id = ui.item.id
               $.pjax.reload({
                          url : "/logist/order/pjax-add-company",
                          container: "#companies",
//                          dataType:"json",
                          type: "POST", 
                        data: {  
                              "id_user" : ui.item.id 
                         }
                         
                       });
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
    <h2 id="label">Новый клиент</h2>
    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
        'validationUrl' => \yii\helpers\Url::to(['/company/validate-add-company']),
        'fieldConfig' => [
            'labelOptions' => ['class' => 'col-lg-12 control-label'],
        ],
    ]);?>

    <div class="col-lg-4">
    <?= $form->field($profile, 'id_user')->hiddenInput(['id' => 'id_user'])->label(false)?>
    <?= $form->field($profile, 'surname')->input('text',  ['id' => 'surname'])?>
    <?= $form->field($profile, 'name')->input('text',  ['id' => 'name'])?>
    <?= $form->field($profile, 'patrinimic')->input('text',  ['id' => 'patrinimic'])?>
    </div>
    <div class="col-lg-4">
        <?= $form->field($user, 'username')->input('tel',  ['id' => 'username'])?>
        <?= $form->field($user, 'email')->input('email',  ['id' => 'email'])?>
        <?= $form->field($profile, 'phone2')->input('tel',  ['id' => 'phone2'])?>
        <?= $form->field($profile, 'email2')->input('email',  ['id' => 'email2'])?>
    </div>
    <?php Pjax::begin(['id' => 'companies',
    ])?>

    <div class="col-lg-12">
<!--        --><?//= ($modelOrder->type_payment == \app\models\Payment::TYPE_BANK_TRANSFER)
//            ? $this->render('@app/views/company/_form', [
//                'form' => $form,
//                'modelCompany' => $modelCompany,
//                'XcompanyXprofile' => $XcompanyXprofile
//            ])
//            : '';
//        ?>
    </div>
<?php Pjax::end()?>
    <div class="col-lg-12">
    <?= Html::submitButton('Оформить заказ', ['class' => 'btn btn-success', 'name' => 'button', 'value' => 'logist_finish'])?>
    </div>
    <?php $form::end()?>

</div>
<script>
    $(function () {

    });
</script>