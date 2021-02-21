<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 09.09.2019
 * Time: 8:49
 */
/* @var $this \yii\web\View
 */
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = Html::encode('Регистрация повторного заказа.');
?>

<h4><?=$this->title?></h4>
<comment>
    <p>Автовладелец может регистрировать принятые не через сервис заказы. Например после выполнения какого то заказа, принятого через наш сервис
    , через какое то время Клиент позвонил и заказал услугу повторно. Или во время выполнения, принятого через сервис, заказа
        Клиент сразу заказал услугу повторно, например, на следующий день. Или в других случаях.</p>
    <p>
        Это - партнерский подход. Соответственно, такие заказы влияют на рейтинг автовладельца со всеми вытекающими...
    </p>
</comment>
<br><br>

<?php
    $form = ActiveForm::begin();
?>
<?= $form->field($modelOrder, 'id_vehicle')->radioList($vehicles, [
    'encode' => false
])->label('Выберите ТС:')?>
<?= $form->field($modelOrder, 'id_driver')->radioList($driversArr)
    ->label('Выберите водителя:')
?>
<?= Html::submitButton('Далее', [
    'class' => 'btn btn-success',
    'name' => 'button',
    'value' => 'next1'
])?>
<?php
    $form::end();
?>
