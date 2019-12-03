<?php
/* @var $this \yii\web\View */
/* @var $content string */

use yii\bootstrap\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\jui\AutoComplete;
use yii\web\JsExpression;

AppAsset::register($this);
\yii\helpers\Url::remember(); //Сохраняет адрес текущей страницы. Для кнопеи назад Url::previous().

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <script
            src="https://api-maps.yandex.ru/2.1/?apikey=16eacdd2-acfd-4122-b0c7-639d10363985&lang=ru_RU" type="text/javascript">
    </script>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'perevozki40.ru',
        'brandUrl' => '/',
        'options' => [
            'class' => 'navbar-default navbar-fixed-top',
            'id' => 'menu',
        ],
    ]);
    echo Nav::widget([
        'encodeLabels' => false,
        'options' => [
            'class' => 'navbar-nav navbar-left',
        ],
        'items' => [
            ['label' => Html::icon('home'), 'url' => '/logist'],
            ['label' => 'Прайс', 'url' => '/price-zone'],
            ['label' => 'Заказы', 'url' => '/logist/order'],
            ['label' => 'Машины', 'url' => '/logist/vehicle'],
            [
                'label' => 'Бухгалтерия',
                'url' => '/finance',
                'visible' => Yii::$app->user->can('admin')
            ],
            Yii::$app->user->isGuest ? (
                ['label' => 'Войти', 'url' => ['/default/login']]
            ) : (
                '<li>'
                . Html::beginForm(['/default/logout'], 'post')
                . Html::submitButton(
                    'Выход (' . Yii::$app->user->identity->profile->name . ' ' . Yii::$app->user->identity->profile->surname . ')',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm()
                . '</li>'
            ),
            (Yii::$app->user->can('admin')) ? (
            ['label' => 'Adminka', 'url' => ['/admin']]
            ) : (
                    ''
            ),
            (Yii::$app->user->can('admin')) ? (
            ['label' => 'Roles', 'url' => ['/admin/roles']]
            ) : (
            ''
            ),
        ],
    ]);
    echo Html::input('text', '', \yii\helpers\Url::to('/admin/users/view', true)
        , ['id' => 'urlTo', 'hidden' => true]);
    echo AutoComplete::widget([
        'clientOptions' => [
            'source' => \app\models\Profile::getArrayForAutoComplete(false),
            'autoFill' => true,
            'minLength' => '0',
            'select' => new JsExpression('function(event, ui) {
                location.href = $("#urlTo").val() + "/?id=" + ui.item.id;
               $("#label").html("Клиент");
               $("#id").val(ui.item.id);
//               alert($(this).val());
               $("#username").val(ui.item.phone);
               $("#phone2").val(ui.item.phone2);
               $("#email").val(ui.item.email);
               $("#email2").val(ui.item.email2);
               $("#name").val(ui.item.name);
               $("#surname").val(ui.item.surname);
               $("#patrinimic").val(ui.item.patrinimic);
               var id = ui.item.id;
            }'),
            'response' => new JsExpression('function(event, ui) {
               $("#username").val($(this).val());
            }'),
            'change' => new JsExpression('function(event, ui) {
                if(!ui.item) {
                     $("#label").html("Новый клиент");
                       $("#id").val("");
//                       $("#username").val("");
                       $("#phone2").val("");
                       $("#email").val("");
                       $("#email2").val("");
                       $("#name").val("");
                       $("#surname").val("");
                       $("#patrinimic").val("");
                       $("#surname").focus();
                        
                }
            }'),
        ],
        'options' => [
            'id' => 'search-all',
            'class' => 'form-control',
            'placeholder' => Yii::t('app', 'Поиск...'),
        ]
    ]);

    NavBar::end();
    ?>

    <div class="container">
        <br><br>
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
        <?= $content ?>
    </div>
</div>


<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>
        <?=Html::a('Соглашением об использовании сервиса perevozki40.ru',
            '/default/user-agreement'
        )?>
        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
<script>
    $(function () {
        setInterval(() => $.pjax.reload({container:'#pjax_new_orders'}), 2*60*1000);
        setInterval(() => $.pjax.reload({container:'#pjax_in_proccess_orders'}), 5*60*1000);
        setInterval(() => $.pjax.reload({container:'#pjax_finished_orders'}), 6*60*1000);
        setInterval(() => $.pjax.reload({container:'#pjax_canceled_orders'}), 5*60*1000);
    });
</script>
</html>
<?php $this->endPage() ?>
