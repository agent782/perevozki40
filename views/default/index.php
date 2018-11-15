<?php
    use yii\bootstrap\Html;
?>

<?php
    if(Yii::$app->user->can('user')):
?>

        <div class="row" id="homeContentForUser">
            <div class="col-lg-3" id="regClient">
                РЕГИСТРАЦИЯ КЛИЕНТА<br>
                <a href="/user/signup-client"><button class="btn btn-primary">РЕГИСТРАЦИЯ</button></a>
            </div>
            <div class="col-lg-5" id="newOrder">
                СДЕЛАТЬ ЗАКАЗ<br>
                <button class="btn btn-success">СДЕЛАТЬ ЗАКАЗ</button>
            </div>
            <div class="col-lg-3" id="regVehicle">
                РЕГИСТРАЦИЯ ВОДИТЕЛЯ<br>
                <button class="btn btn-primary">РЕГИСТРАЦИЯ</button>
            </div>
        </div>

<?php
    endif;
?>

