<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 20.06.2019
 * Time: 13:47
 */
use yii\helpers\Url;
use yii\bootstrap\Html;
?>

<div class="container">
    <br><br><br>
    <label class="h2">Системные инструменты.</label>
    <br><br>
    <?= Html::a('Очистить журнал заказов', Url::to('delete-all-orders'), [
        'class' => 'btn btn-warning',
        'data-confirm' => Yii::t('yii'
            , 'Вы уверены?'),
    ])?>
    <br><br>
    <?= Html::a('Очистить прайс-лист', Url::to('delete-all-price'), [
        'class' => 'btn btn-warning',
        'data-confirm' => Yii::t('yii'
            , 'Вы уверены?'),
    ])?>

    <?= Html::a('Очистить список пользователей', Url::to('delete-all-users'), [
        'class' => 'btn btn-warning',
        'data-confirm' => Yii::t('yii'
            , 'Вы уверены?'),
    ])?>
</div>
