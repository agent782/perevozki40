<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 17.01.2018
 * Time: 17:51
 */
use yii\bootstrap\Html;
use yii\grid\GridView;

$this->title = Html::encode('Пользователи');
echo  mb_ereg_replace("[^0-9]",'',$modelUser->username).'<hr>';
echo GridView::widget([
    'dataProvider' => $dataUserProvider,
    'filterModel' => $searchUserModel,
    'columns' => [
        'id',
        'username',
        'profile.name',
        'role',
//            'dateCreatedAt'

    ],
]);