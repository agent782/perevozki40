<?php
    use yii\bootstrap\Html;
    use yii\helpers\Url;
    use lesha724\youtubewidget\Youtube;
//    \app\components\functions\emails::sendAfterUserRegistration(Yii::$app->user->id);
?>
<!--<script src="https://e-timer.ru/js/etimer.js"></script>-->

<div class="row">
    <div class="alert-warning" style="text-align: center">
<!--        <label class="h2">-->
<!--            <p>ОТКРЫТИЕ 15 ноября 2019 года!!!</p>-->
<!--            <script type="text/javascript">-->
<!--                jQuery(document).ready(function() {-->
<!--                    jQuery(".eTimer").eTimer({-->
<!--                        etType: 0, etDate: "15.11.2019.9.1", etTitleText: "", etTitleSize: 17, etShowSign: 1,-->
<!--                        etSep: ":", etFontFamily: "Trebuchet MS", etTextColor: "#a3a3a3", etPaddingTB: 15,-->
<!--                        etPaddingLR: 15, etBackground: "#333333", etBorderSize: 0, etBorderRadius: 2, etBorderColor: "white", etShadow: " 0px 0px 10px 0px #333333", etLastUnit: 4, etNumberFontFamily: "Impact", etNumberSize: 35, etNumberColor: "white", etNumberPaddingTB: 0, etNumberPaddingLR: 8, etNumberBackground: "#11abb0", etNumberBorderSize: 0, etNumberBorderRadius: 5, etNumberBorderColor: "white", etNumberShadow: "inset 0px 0px 10px 0px rgba(0, 0, 0, 0.5)"-->
<!--                    });-->
<!--                });-->
<!--            </script>-->
<!--            <p><div class="eTimer"></div></p>-->
<!--            Сервис находится на стадии тестирования. Уже скоро будут доступны все возможности!-->
<!--        </label>-->
        <p><?= Html::a('Рассчитать стоимость', '/order/create',
            ['class' => 'btn btn-lg btn-danger'])?>
        </p>
        <?=
            Youtube::widget([
                'video' => 'QNSNgNL308E',
                'iframeOptions'=>[ /*for container iframe*/
                    'class'=>'youtube-video'
                ],
//                'divOptions'=>[ /*for container div*/
//                    'class'=>'youtube-video-div'
//                ],
                'playerVars'=> [
                    'autoplay' => 1,
                ],
                'width' => 320
            ])
        ?>
    </div>

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
    <div class="general-dark">
        <label>
            Сервис Региональных Перевозок
        </label>
        <div>
            <p>Обнинск, Балабаново, Боровск, Ворсино, Малоярославец, Белоусово, Жуков, Ермолино, Наро-Фоминск, Калужская область</p>
            <p>Под Ваш заказ выделяется отдельное ТС. </p>
            <p>Не попутные и не сборные грузы!</p>
        </div>
    </div>
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
        <p>
            <?php
                if(!Yii::$app->user->isGuest)
                    echo '<br>' . Html::a('Бесплатная <br> регистрация', '/default/signup', ['class' => 'btn btn-lg btn-warning'])
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