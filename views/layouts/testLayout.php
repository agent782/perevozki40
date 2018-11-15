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
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
    <?php $this->beginBody() ?>
    <div class="container">
        <div class="row">
            <div class="container-fluid text-center text-primary">
                <h3>PEREVOZKI40.RU</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 bg-primary text-center">
                <?= $content ?>
            </div>
            <div class="col-lg-4 bg-danger text-center">
                <br>2222<br><br>
            </div>
            <div class="col-lg-4 bg-info text-center">
                <br>3333<br><br>
            </div>
        </div>
    </div>



    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>