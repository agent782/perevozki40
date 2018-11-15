<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 31.01.2018
 * Time: 14:38
 */
//$this->registerCssFile("https://cdn.jsdelivr.net/npm/suggestions-jquery@17.10.1/dist/css/suggestions.min.css");
//$this->registerJsFile("https://cdn.jsdelivr.net/npm/suggestions-jquery@17.10.1/dist/js/jquery.suggestions.min.js");
//$this->registerJsFile('/js/jquery-dateFormat.js');
//$this->registerJsFile('/js/addCompany.js');
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\widgets\MaskedInput;

$this->title = Html::encode('Регистрация юридического лица.');
?>

Спасибо. Вы харегистрировались как клиент сервиса perevozki40.
<br>
Для заключения договора осталось добавить юридическое лицо
Введите ИНН и заполните реквизиты Вашей организации.
<!--<input id="party" name="party" type="text" size="100"/>-->

<?= $this->render('_form', [
    'modelCompany' => $modelCompany,
    'XcompanyXprofile' => $XcompanyXprofile,
]) ?>

