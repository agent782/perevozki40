<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 03.02.2020
 * Time: 14:44
 */
/* @var $profile \app\models\Profile;
 * @var $this \yii\web\View;
 *
 */
 use yii\bootstrap\Html;
 use yii\bootstrap\Tabs;
?>

<?=
   Tabs::widget([
       'items' => [
           [
               'label' => 'Роли',
               'content' => $this->render('user-administration/roles',[
                   'profile' => $profile
               ])
           ]
       ]
   ]);
?>


