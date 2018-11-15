<?php
/* @var $this \yii\web\View */
/* @var $content string */
use yii\bootstrap\ButtonDropdown;
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">

    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div  class="container visible-sm visible-xs ">
    <div class="row navbar-default navbar-fixed-top"></div>
        <div class="col-xs-3">
            <div class="btn-group">
                <button type="button" class="btn btn-danger">Action</button>
                <button type="button" class="btn btn-danger dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <a class="dropdown-item" href="#">Something else here</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">Separated link</a>
                </div>
        </div>
        <div  class="col-xs-6">
            <div class="row">
                <a style="font-size: 18px ; font-stretch: extra-expanded; font-weight: bolder" href="/<?php Yii::$app->homeUrl?>"> <img src="/img/icons/cargo-20.png"/>perevozki40.ru</a>
            </div>
            <div class="row" >
                <a style="font-size: 14px; font-weight: bold" href="tel:+74843955888"><img src="/img/icons/phone-20.png"/>+7(48439)55-888</a>
            </div>

        </div>
        <div class="col-xs-3">

        </div>
    </div>

    <div class="container" style="position: relative; top: 100px">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
            <?= Yii::$app->user->identity->username ?>
            <?php
            var_dump(Yii::$app->session)
            ?>

        <?= $content ?>
    </div>





<footer class="footer">
    <div class="container">

    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
