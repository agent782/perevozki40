<?php
/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => '<i class="fas fa-truck"></i> perevozki40.ru',
//        'brandLabel' => '<img src="img/logo_100_400.png" class="img-responsive" alt="PEREVOZKI40.RU" >',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-default my-navbar navbar-fixed-top',
//            'class' => ' navbar navbar-inverse navbar-fixed-top',
            'id' => 'menu',
//            'renderInnerContainer' => true,
        ],
    ]);

    echo Nav::widget([
        'options' => [
            'class' => 'navbar-nav navbar-center'
        ],
        'items' => [

            ['label' => 'Возможности сервиса', 'url' => ['']],
            ['label' => 'Расчет стоимости', 'url' => ['']],
            ['label' => 'Сотрудничество', 'url' => ['']],
            ['label' => 'Партнеры', 'url' => ['']],
            [
                'label' => 'Личный кабинет',
                'class' => 'btn btn-primary',
                'type' => 'button',
//                    'data-toggle' => 'dropdown',

                'items' => [
                    ['label' => 'item1', 'url' => ''],
                    ['label' => 'item2', 'url' => ''],
                ],
                'visible' => !Yii::$app->user->isGuest,

            ],
            Yii::$app->user->isGuest ? (
            ['label' => 'Login', 'url' => ['/default/login']]
            ) : (
                '<li>'
                . Html::beginForm(['/default/logout'], 'post')
                . Html::submitButton(
                    'Logout (' . Yii::$app->user->identity->username . ')',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm()
                . '</li>'
            ),

            (Yii::$app->user->can('admin')) ? (
            [   'label' => 'Adminka',
                'url' => ['/admin'],
                'items' => [
                    ['label' => 'Roles', 'url' => ['/admin/roles']]
                ]
            ]
            ) : (
            null
            ),
        ],
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
        <i class="fas fa-truck"></i>perev<img src="/img/icons/wheel.png"/>zki40.ru'
    </div>
</div>


<footer class="footer">
    <div class="container">

    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
