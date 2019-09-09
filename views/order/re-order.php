<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 09.09.2019
 * Time: 8:49
 */
/* @var $this \yii\web\View
 */
use yii\bootstrap\Html;

$this->title = Html::encode('Регистрация повторного заказа.');
?>

<h4><?=$this->title?></h4>
<?= Html::a('Принял', '/order/re-order-new', ['class' => 'btn btn-lg btn-success'])?>
<p>или</p>
<?= Html::a('Принял и выполнил', '/order/re-order-finish', ['class' => 'btn btn-lg btn-success'])?>
<br><br>

