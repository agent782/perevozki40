<?php
    use yii\bootstrap\Html;
    use yii\helpers\Url;
//    \app\components\functions\emails::sendAfterUserRegistration(Yii::$app->user->id);
?>

<div class="container">
<?php
    if(Yii::$app->user->can('user')):
        $carusel_user_reg = [
            [
                'content' => '<div> ЗАВЕРШИТЬ РЕГИСТРАЦИЮ КЛИЕНТА<br><br>
                   <p style="text-align: left">Вы сможете:</p>
                     <comment style="text-align: center">
                        <p>Заказывать услуги автотранспорта</p>
                        <p>Получать on-line счета, акты, ...</p>
                        <p>Просматривать статистику</p>
                        <p>и многое другое!</p>
                </comment>
                <p align="right">
                Бесплатно! <a href="/user/signup-client"><button class="btn btn-lg btn-primary">ЗАВЕРШИТЬ</button></a>
                </p></div>',
                'options' => ['class' => 'homeContentForUser']
            ],
            [
                'content' => '<div>
                    ЗАВЕРШИТЬ РЕГИСТРАЦИЮ ВОДИТЕЛЯ<br><br>
                    <p style="text-align: left">Вы сможете:</p>
                    <comment style="text-align: center">
                        <p>Регистрировать ТС</p>
                        <p>Принимать заказы</p>
                        <p>Просматривать статистику</p>
                        <p>и многое другое!</p>
                </comment>
                <p align="right">
                    <br>
                    Бесплатно! <a href="/car-owner/create"> <button class="btn btn-lg btn-primary">ЗАВЕРШИТЬ</button></a>
                </p></div>
                ',
                'options' => ['class' => 'homeContentForUser']
            ]
        ];
    echo \yii\bootstrap\Carousel::widget([
        'items' => $carusel_user_reg,
        'options' => ['class' => 'carousel slide', 'data-interval' => '5000'],
        'controls' => false
    ])
?>
<?php
    endif;
?>

</div>
