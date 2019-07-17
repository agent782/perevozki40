<?php
    use yii\bootstrap\Html;
    use yii\helpers\Url;
//    \app\components\functions\emails::sendAfterUserRegistration(Yii::$app->user->id);
?>

<div class="container">
<?php
    if(Yii::$app->user->can('user')):
?>
        <div class="row" id="homeContentForUser">
            <div class="col-lg-5" id="regClient">
                ЗАВЕРШИТЬ РЕГИСТРАЦИЮ КЛИЕНТА<br><br>
                <comment style="text-align: left">
                    Вы сможете:
                    <ul>
                        <li>Заказывать услуги автотранспорта</li>
                        <li>Получать on-line счета, акты, ...</li>
                        <li>Просматривать статистику</li>
                        <li>и многое другое!</li>
                    </ul>
                </comment>
                <p align="right">
                Бесплатно! <a href="/user/signup-client"><button class="btn btn-lg btn-primary">ЗАВЕРШИТЬ</button></a>
                </p>
            </div>

            <div class="col-lg-5" id="regVehicle">
                ЗАВЕРШИТЬ РЕГИСТРАЦИЮ ВОДИТЕЛЯ<br><br>
                <comment style="text-align: left">
                    Вы сможете:
                    <ul>
                        <li>Регистрировать ТС</li>
                        <li>Принимать заказы</li>
                        <li>Просматривать статистику</li>
                        <li>и многое другое!</li>
                    </ul>
                </comment>
                <p align="right">
                    Бесплатно! <a href="/car-owner/create"> <button class="btn btn-primary">ЗАВЕРШИТЬ</button></a>
                </p>
            </div>
        </div>

<?php
    endif;
?>

</div>
