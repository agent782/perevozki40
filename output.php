<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->title = 'My Yii Application';
//$this->registerJsFile('@web/js/map.js');
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Congratulations!</h1>

        <p class="lead">You have successfully created your Yii-powered application.</p>

        <a class="btn btn-lg btn-success" href="http://www.yiiframework.com">Get started with Yii</a></p>
    </div>

    <?php echo Yii::$app->user->identity->username;?>

</div>
<?//= var_dump(Yii::$app->session->getFlash('access'));?>
<?php


if(Yii::$app->session->hasFlash('access')){
      echo Yii::$app->session->getFlash('access');
    } elseif(Yii::$app->session->hasFlash('error')){
        echo Yii::$app->session->getFlash('error');
    }
?>

<?php $form = ActiveForm::begin()?>
    <?= $form->field($addRole, 'name') ?>
    <?= Html::submitButton('SAVE',['class' => 'btn btn-success']) ?>
<?php ActiveForm::end()?>


