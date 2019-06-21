<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 16.01.2018
 * Time: 12:30
 */
use yii\bootstrap\Html;
use yii\grid\GridView;

$this->title = Html::encode('Пользователи');
echo  mb_ereg_replace("[^0-9]",'',$modelUser->username).'<hr>';
echo GridView::widget([
    'rowOptions' => function ($model, $key, $index, $grid) {
        return ['id' => $model['id'], 'onclick' => 'alert(this.id);'];
    },
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'tableOptions' => [
        'class' => 'table table-striped table-bordered'
    ],
    'columns' => [
        'id',
        'username',
        [
            'attribute' => 'profile.photoFullPath',
            'format' => ['image',['width'=>'60']],
            'value' => function($model) {
                if($model->profile) {
                    return $model->profile->photoFullPath;
                }
            },
        ],
//        'profile.photoFullPath:image',
        'profile.name',
        'status',
//            'dateCreatedAt'
        ['class' => 'yii\grid\ActionColumn'],

    ],
]);

