<?php
    use yii\bootstrap\Html;
    use yii\helpers\Url;
//    \app\components\functions\emails::sendAfterUserRegistration(Yii::$app->user->id);
?>

<div class="row">
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
        'options' => ['class' => 'container carousel slide', 'data-interval' => '3000'],
        'controls' => false
    ])
?>
<?php
    endif;
?>
    <div class="general-light">
        <label>
            Новые возможности
        </label>
        <p>Удобный конструктор заказа ТС</p>
        <p>Прозрачное и максимально гибкое ценообразование</p>
        <p>Sms, push, email информирование о статусе заказа</p>
        <p>Автоматическое оформление Договоров, счетов, актов и возможность скачивания в Личном кабинете</p>
        <p>Система оценок и отзывов</p>
    </div>
<div class="general-dark">
    <label>
        Как это работает?
    </label>
    <div>
        <p>Клиент в удобном конструкторе оформляет заказ</p>
        <p>Выбирает подходящие тарифы</p>
        <p>Система отправляет информацию водителям подходящих ТС</p>
        <p>Водитель подтверждает получение заказа</p>
        <p>После выполнения заказа Клиент получает уведомление о завершении</p>
            <?php
                if(!Yii::$app->user->isGuest)
                    echo '<br>' . Html::a('Бесплатная <br> регистрация', 'signup', ['class' => 'btn btn-lg btn-warning'])
            ?>
        </p>
    </div>
</div>
    <div class="general-light">
        <label>
            Транспорт
        </label>
        <p>Грузовой и пассажирский транспорт. Спецтехника</p>
        <p>От "каблучка" до "фуры"</p>
        <p></p>
        <p>Мы сотрудничаем с проверенными годами водителями</p>
        <p>Естественный отбор благодаря системе оценок и отзывов</p>
        <br>
        <?= Html::a('Попробовать', '/order/create', ['class' => 'btn btn-lg btn-primary'])?>
    </div>
</div>