<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 11.06.2019
 * Time: 11:08
 */
use yii\bootstrap\Html;
use yii\helpers\Url;
use kartik\grid\GridView;

    echo GridView::widget([
        'dataProvider' => $dataProvider,
       'filterModel' => $searchModel,
       'tableOptions' => ['style' => 'width: auto;'],
       'columns' => [
           'id_user',
           'fioFull',
           [
               'label' => 'Новые данные',
               'format' => 'raw',
               'value' => function($profile){
                   $res = '';
                   foreach ($profile->getPublicAttributes() as $attribute=>$value){
                       $openTag = '';
                       $closeTag = '';
                       if($value != $profile->update_to_check[$attribute]){
                           $openTag = '<strong>';
                           $closeTag = '</strong>';
                       }
                       $res .= $openTag . $profile->update_to_check[$attribute] . $closeTag . '<br>';
                   }
                   if($profile->update_to_check['passport_number']){

                       $res .= $profile->update_to_check['passport_number'] . '<br>';
                       $res .= $profile->update_to_check['passport_date'] . '<br>';
                       $res .= $profile->update_to_check['passport_place'] . '<br>';
                       $res .= \app\models\Country::findOne($profile->update_to_check['country'])->name . '<br>';
                   }
                   if($profile->update_to_check['photo']){
                        $res .= Html::img($profile->urlUpdatePhoto, ['style'=>'width: auto; height: 100px;']);
                   }
                   return $res;
               }
           ],
           [
               'label' => 'Старые данные',
               'format' => 'raw',
               'value' => function($profile){
                    $res = '';
                    foreach ($profile->getPublicAttributes() as $attribute=>$value){
                        $res .= $value . '<br>';
                    }
                   if($profile->passport){
                       $res .= $profile->passport->number .  '<br>';
                       $res .= $profile->passport->date .  '<br>';
                       $res .= $profile->passport->place .  '<br>';
                       $res .= \app\models\Country::findOne($profile->passport->country)->name .  '<br>';
                   }
                   $res .= Html::img($profile->urlPhoto, ['style'=>'width: auto; height: 100px']);

                    return $res;
               }
           ],
           [
               'label' => 'Действия',
               'format' => 'raw',
               'value' => function($profile){
                    return Html::a(Html::icon('ok-sign'), Url::to(['/admin/users/confirm-profile-update', 'id_user' => $profile->id_user]))
                        . ' '
                        . Html::a(Html::icon('remove-sign'), Url::to(['/admin/users/cancel-profile-update', 'id_user' => $profile->id_user]))
                        ;
               }
           ]
       ]
    ]);

    ?>

