<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 23.05.2019
 * Time: 9:30
 */
    use Yii;
    use yii\bootstrap\Html;
    use yii\helpers\Url;
?>

    Здравствуйте, <?= $name?>!
<br>
Благодарим Вас за регистрацию в качестве Водителя на сервисе региональных перевозок perevozki40.ru.
<br>
Надеемся на долгосрочное сотрудничество!
<br>
Полную информацию о возможностях сервиса и ценах Вы всегда можете посмотреть на нашем сайте!
<br>
Для входа в <?=Html::a('Личный кабинет', Url::to('/user', 'http'))?> используйте введенные при регистрации номер телефона и пароль.

