<?php
use yii\bootstrap\Html;
use yii\helpers\Url;
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 17.01.2019
 * Time: 12:27
 */
?>

Добрый день, <?=$name?>!
<br><br>
<?php
    if($type == \app\models\Invoice::TYPE_INVOICE):
?>
        Счет за выполненный заказ выставлен. Вы можете скачать его в Личном кабинете в разделе "Заказы" на вкладке "Завершенные".
        Также, для Вашего удобства, файл прикреплен к данному письму. Пожалуйста, оплачивайте счета в установленные сроки. Срок оплаты существенно влияет на рейтинг Клиента!

<?php
    endif;
?>
<?php
if($type == \app\models\Invoice::TYPE_CERTIFICATE):
    ?>
    Акт выполненных работ сформирован. Вы можете скачать его в Личном кабинете в разделе "Заказы" на вкладке "Завершенные".
    Также, для Вашего удобства, файл прикреплен к данному письму.
    Пожалуйста, подпишите и отправьте нам скан в течении 3-х дней.
    Срок получения нами подписанного скана существенно влияет на рейтинг Клиента и Организации.
    Вы можете отправить скан в ответ на это письмо или загрузить в Личном кабинете в разделе "Заказы" на вкладке "Завершенные".
    <?php
endif;
?>
<br>
Для входа в <?=Html::a('Личный кабинет', Url::to('/user', 'http'))?> используйте введенные при регистрации номер телефона и пароль.
<br><br>
Спасибо, что Вы с нами!


