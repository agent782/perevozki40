<?php
/* @var $this \yii\web\View */
/* @var $content string */
use yii\bootstrap\Button;
use yii\bootstrap\ButtonDropdown;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\widgets;
use app\models\Message;
use yii\bootstrap\Html;
use yii\helpers\Url;
AppAsset::register($this);


//$this->registerJsFile('/js/hideMenu.js');
//$this->registerCss('
//    .navbar-fixed-top {
//	position: fixed;
//	top: 0px;
//}
//');
$this->params['breadcrumbs'][] = $this->title;
$this->title = 'perevozki40.ru Сервис Региональных Грузоперевозок';
\yii\helpers\Url::remember(); //Сохраняет адрес текущей страницы. Для кнопеи назад Url::previous().
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <!-- Yandex.Metrika counter -->
    <script type="text/javascript" >
        (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym(54762694, "init", {
            clickmap:true,
            trackLinks:true,
            accurateTrackBounce:true
        });
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/54762694" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <!-- /Yandex.Metrika counter -->
    <title><?= Html::encode($this->title) ?></title>
    <script
            src="https://api-maps.yandex.ru/2.1/?apikey=16eacdd2-acfd-4122-b0c7-639d10363985&lang=ru_RU" type="text/javascript">
    </script>
<!--    <script charset="UTF-8" src="//cdn.sendpulse.com/js/push/ed2cce3784ec8246afa34633f9a33b2a_0.js" async></script>-->
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="container navbar-default navbar-fixed-top
<!--visible-sm visible-xs-->
" id="menu-mobile" align="center">
<!--    <div class="row">-->
        <div class="col-xs-3">
            <?php
//                Без этой строчки не выпадают остальные меню
                \yii\bootstrap\ButtonDropdown::widget();
            ?>
            <div class="btn-group"> <!-- btn group 2, primary -->
                <button type="button" class="dropdown-toggle" data-toggle="dropdown">
                    <img src="/img/icons/menu.png" alt="Меню"/></button>
                <!-- Dropdown list -->
                <div class="dropdown-menu"  role="menu">
                    <?php
                    $menu_items = [
                        [
                            'label' => 'Главная',
                            'url' => Yii::$app->homeUrl,
                        ],
                        [
                            'label' => 'Тарифные зоны',
                            'url' => '/price-zone'
                        ],
                        [
                            'label' => 'О сервисе',
                        ],
                        [
                            'label' => 'Партнеры',
                        ],
                        [
                            'label' => 'Контакты',
                            'url' => Url::to('/default/contacts')
                        ]
                    ];
                    ?>
                    <?=
                    Nav::widget([
                        'options' => [
                            'class' => 'menu',
                            'style' => [
//                                'font-size' => '24px',
                            ],
                        ],
                        'items' => $menu_items,
                    ]);
                    ?>
                </div>
            </div>
<!--            <img src="/img/icons/menu.png" alt="Меню"/>-->
        </div>
        <div  class="col-xs-6">
            <div class="row" style="font-size: 20px; font-weight: 900">
                <a href="/<?php Yii::$app->homeUrl?>"><img src="/img/icons/cargo-20.png"> perevozki40.ru</a>
            </div>
            <div class="row" style="font-size: 16px; font-weight: 700">
                <a href="tel:+74843990949"><img src="/img/icons/phone-20.png">+7(484)399-09-49</a>
                <a href="tel:+79105234777">+7(910)523-47-77</a>
            </div>

        </div>
<!--        <div class="col-xs-1">1111111111</div>-->
        <div class="col-xs-1 visible-md visible-lg">
            <?php
//                widgets\Pjax::begin(['id' => 'pjax_message']);
            if(Yii::$app->user->id) {

                if (Message::countNewMessage(Yii::$app->user->id)) {
                    echo Html::a(
                        Html::img('/img/icons/notification-48.png'
                        ) . '(+' . Message::countNewMessage(Yii::$app->user->id) . ')'
                        , Url::to(['/message']
                    ));
                } else {
                    echo Html::a(
                        Html::img('/img/icons/message-48.png')
                        , Url::to(['/message']));
                }
            }

//                widgets\Pjax::end();
            ?>
        </div>
        <div class="col-xs-2">
            <?php
                if((Yii::$app->user->isGuest)):
            ?>
                    <a href="/default/login"><button type="button"> <img src="/img/icons/cabinet.png" alt="Меню"/></button></a>
            <?php
                else:
                    $cabinet_items = [
                        [
                            'label' => 'Диспетчерская',
                            'url' => '/logist',
                            'visible' => Yii::$app->user->can('logist') || Yii::$app->user->can( 'admin')
                        ],
                        [
                            'label' => 'Админка',
                            'url' => '/admin',
                            'visible' => Yii::$app->user->can('admin'),
                        ],
                        [
                            'label' => 'Роли',
                            'url' => '/admin/roles',
                            'visible' => Yii::$app->user->can('admin'),
                        ],
                        [
                            'label' => 'Пользователи',
                            'url' => '/admin/users',
                            'visible' => Yii::$app->user->can('admin'),
                        ],
                        [
                            'label' => 'Проверка изменения пользователей',
                            'url' => '/admin/users/check-users-updates',
                            'visible' => Yii::$app->user->can('admin'),
                        ],
                        [
                            'label' => 'Подсказки',
                            'url' => '/tip',
                            'visible' => Yii::$app->user->can('admin'),
                        ],
                        [
                            'label' => 'Профиль ('.Yii::$app->user->identity->profile->name . ' ' . Yii::$app->user->identity->profile->surname.')',
                            'url' => '/user',
                        ],
                        [
                            'label' => 'Баланс',
                            'url' => '/user/balance',
                        ],
                        [
                            'label' => 'Сделать новый заказ',
                            'url' => Url::to(['/order/create', 'user_id' => Yii::$app->user->id]),
                            'visible' => !Yii::$app->user->isGuest
                        ],
                        [
                            'label' => (Message::countNewMessage(Yii::$app->user->id))
                                ?'Уведомления ' .
                                    '<b class="incube-invert">' . \app\models\Message::countNewMessage(Yii::$app->user->id) . '</b>'
                                :'Уведомления',
                            'url' => '/message',
                            'class' => 'incircle',
                            'encode' => false,
                        ],
                        [
                            'label' => 'Заказы (Водитель)',
                            'url' => '/order/vehicle',
                            'visible' => Yii::$app->user->can('car_owner'),
                        ],
                        [
                            'label' => 'Мой транспорт',
                            'url' => '/vehicle',
                            'visible' => Yii::$app->user->can('car_owner'),
                        ],
                        [
                            'label' => 'Мои водители',
                            'url' => '/driver',
                            'visible' => Yii::$app->user->can('car_owner'),
                        ],
                        [
                            'label' => 'Юридические лица',
                            'url' => '/company',
                            'visible' => (Yii::$app->user->can('client')
                                || Yii::$app->user->can('car_owner'))
                        ],
                        [
                            'label' => 'Запрос на выплату',
                            'url' => '/request-payment/create',
                            'visible' => Yii::$app->user->can('car-owner')
                        ],
                        [
                            'label' => 'Заказы (Клиент)',
                            'url' => '/order/client',
                            'visible' => !Yii::$app->user->isGuest
                        ],
                        [
                            'label' => 'Договора с клиентами',
                            'url' => '/document',
                            'visible' => Yii::$app->user->can('admin'),
                        ],
                        [
                            'label' => 'Доверенности клиентов',
                            'url' => '/poa',
                            'visible' => Yii::$app->user->can('admin'),
                        ],
                        [
                            'label' => 'ТС',
                            'url' => '/logist/vehicle',
                            'visible' => Yii::$app->user->can('admin'),
                        ],
                        [
                            'label' => 'Бухгалтерия',
                            'url' => '/finance',
                            'visible' => Yii::$app->user->can('admin'),
                        ],
                        [
                            'label' => 'Завершть регистрацию клиента',
                            'linkOptions' => ['class' => 'btn-primary'],
                            'url' => '/user/signup-client',
                            'visible' => Yii::$app->user->can('user')
                        ],
                        [
                            'label' => 'Регистрация автовладельца',
                            'url' => '/car-owner/create',
                            'visible' => Yii::$app->user->can('user')
                                || Yii::$app->user->can('client'),
//                            'visible' => Yii::$app->user->can('car_owner'),

                        ],
                        [
                            'label' => 'Управление Прайс-листом',
                            'url' => '/price-zone',
                            'visible' => Yii::$app->user->can('admin')
                        ],
                        [
                            'label' => 'Настройки',
                            'url' => '/setting',
                            'visible' => Yii::$app->user->can('admin'),
                        ],
                        [
                            'label' => 'Системные инструменты',
                            'url' => '/admin/default/system-tools',
                            'visible' => Yii::$app->user->can('admin'),
                        ],
                        ['label' => 'Выход', 'url' => '/default/logout']
                    ];
            ?>
                    <div class="btn-group"> <!-- btn group 2, primary -->
                        <button type="button" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="/img/icons/cabinet.png" alt="Меню"/></button>
                        <!-- Dropdown list -->
                        <div class="dropdown-menu"  role="menu">
                            <?=
                            Nav::widget([
                                'options' => [
                                    'class' => 'menu',
                                    'style' => [
//                                        'width' => '50%',
                                    ],
                                ],
                                'items' => $cabinet_items,
                            ]);
                            ?>
                        </div>
                    </div>
            <?php
                endif;
            ?>
            <!--                <img src="/img/icons/cabinet.png" alt="Личный кабинет"/>-->
        </div>
    </div>

</div>


<div class="wrap" style=" padding-top: 150px; margin: 0px 40px 40px 40px; padding-bottom: 80px;">

    <?php if(Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <?= Yii::$app->session->getFlash('success')?>
    </div>
    <?php endif; ?>

    <?php if(Yii::$app->session->hasFlash('info')): ?>
        <div class="container alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?= Yii::$app->session->getFlash('info')?>
        </div>
    <?php endif; ?>

    <?php if(Yii::$app->session->hasFlash('warning')): ?>
        <div class="container alert alert-warning alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?= Yii::$app->session->getFlash('warning')?>
        </div>
    <?php endif; ?>
<!--индикация загрузки-->
    <div class="dm-overlay" id="loader">
        <div class="dm-table">
            <div class="dm-cell">
                <div class="dm-modal">
                    <a href="#close" class="close"></a>
                    <h3>perevozki40.ru</h3>
                    <div class="pl-left">
                        <div class="bubblingG">
	                        <span id="bubblingG_1">
	                        </span>
                            <span id="bubblingG_2">
	                        </span>
                            <span id="bubblingG_3">
	                        </span>
                        </div>
                    </div>
                    <p hidden>perevozki40.ru....</p>
                </div>
            </div>
        </div>
    </div>

<div style='width:auto;margin-top: 10px'>
    <div class="warning">
        <?php
        if(!Yii::$app->user->isGuest)
        {
            $balance = Yii::$app->user->identity->profile->balance;
            $balanceCSS = 'color: green; text-align: center;';
            if($balance['balance'] < 0) $balanceCSS = 'color: red; text-align: center;';

            echo Html::a('<h4 style="' . $balanceCSS . '"><b>Ваш баланс: ' . $balance['balance_text']
                . Html::icon('question-sign') .  '</b></h4>', '/user/balance');
        }
        ?>
    </div>
        <?= $content ?>
</div>
</div>
<footer class="footer">
    <div class="container">
        <p class="pull-left"><?=Html::a('Соглашением об использовании сервиса perevozki40.ru ',
                '/default/user-agreement', ['style' => 'color: white']
            )?> | <?=Html::a(' Соглашение о конфиденциальности ',
                '/default/policy', ['style' => 'color: white']
            )?></p>

        <p class="pull-right">&copy; Григоров Денис Евгеньевич <?= date('Y') ?></p>

    </div>
</footer>


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
