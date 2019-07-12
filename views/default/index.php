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
            <div class="col-lg-3" id="regClient">
                ЗАВЕРШИТЬ РЕГИСТРАЦИЮ КЛИЕНТА<br>
                <a href="/user/signup-client"><button class="btn btn-primary">ЗАВЕРШИТЬ</button></a>
            </div>
            <div class="col-lg-5" id="newOrder">
                СДЕЛАТЬ НОВЫЙ ЗАКАЗ<br>
                <a href="<?=Url::to(['/order/create', 'user_id' => Yii::$app->user->id])?>"><button class="btn btn-success">ЗАКАЗАТЬ</button></a>
            </div>
            <div class="col-lg-3" id="regVehicle">
                ЗАВЕРШИТЬ РЕГИСТРАЦИЮ ВОДИТЕЛЯ<br>
                <a href="/car-owner/create"> <button class="btn btn-primary">ЗАВЕРШИТЬ</button></a>
            </div>
        </div>

<?php
    endif;
?>

</div>
