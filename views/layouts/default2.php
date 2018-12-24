<?php
/* @var $this \yii\web\View */
/* @var $content string */
use yii\bootstrap\Button;
use yii\bootstrap\ButtonDropdown;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\widgets;
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
    <title><?= Html::encode($this->title) ?></title>
<!--    <script charset="UTF-8" src="//cdn.sendpulse.com/js/push/ed2cce3784ec8246afa34633f9a33b2a_0.js" async></script>-->
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="container navbar-default navbar-fixed-top
<!--visible-sm visible-xs-->
" id="menu-mobile" align="center">
    <div class="row">
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
                <a href="tel:+74843955888"><img src="/img/icons/phone-20.png">+7(48439)55-888</a>
            </div>

        </div>
        <div class="col-xs-3">
            <?php
                if((Yii::$app->user->isGuest)):
            ?>
                    <a href="/default/login"><button type="button"> <img src="/img/icons/cabinet.png" alt="Меню"/></button></a>
            <?php
                else:
                    $cabinet_items = [
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
                            'label' => 'Профиль ('.Yii::$app->user->identity->profile->name . ' ' . Yii::$app->user->identity->profile->surname.')',
                            'url' => '/user',
                        ],
                        [
                            'label' => 'Сообщения',
                            'url' => '/message',
                        ],
                        [
                            'label' => 'Юридические лица',
                            'url' => '/company',
                        ],
                        [
                            'label' => 'Сделать новый заказ',
                            'url' => '/order/create'
                        ],
                        [
                            'label' => 'Заказы (Клиент)',
                            'url' => '/order/client'
                        ],
                        [
                            'label' => 'Заказы (Водитель)',
                            'url' => '/order/vehicle'
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
                            'url' => '/admin/vehicle',
                            'visible' => Yii::$app->user->can('admin'),
                        ],
                        [
                            'label' => 'Бухгалтерия',
                            'url' => '/finance',
                            'visible' => Yii::$app->user->can('admin'),
                        ],
                        [
                            'label' => 'Регистрация автовладельца',
                            'url' => '/car-owner/create',
                            'visible' => Yii::$app->user->can('user')
                                || Yii::$app->user->can('client'),
//                            'visible' => Yii::$app->user->can('car_owner'),

                        ],
                        [
                            'label' => 'Мои водители',
                            'url' => '/driver',
                            'visible' => Yii::$app->user->can('car_owner'),
                        ],
                        [
                            'label' => 'Мой транспорт',
                            'url' => '/vehicle',
                            'visible' => Yii::$app->user->can('car_owner'),
                        ],
                        [
                            'label' => 'Управление Прайс-листом',
                            'url' => '/price-zone'
                        ],
                        [
                            'label' => 'Настройки',
                            'url' => '/setting',
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
                                    'class' => 'menu navbar-nav navbar-right',
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


<div class="wrap" style="position: relative; top: 100px; margin: 0px 40px 40px 40px; padding-bottom: 80px;">

        <?= Breadcrumbs::widget([
//            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>

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
                    <p hidden>Текстовое содержание....</p>
                </div>
            </div>
        </div>
    </div>

<div style='overflow-x:scroll;overflow-y:hidden;width:auto;'>
        <?= $content ?>
</div>
    <!--        <i class="fas fa-truck"></i>perev<img src="/img/icons/wheel.png"/>zki40.ru'-->
</div>

<footer class="container-fluid footer">

</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
