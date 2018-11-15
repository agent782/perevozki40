<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 11.01.2018
 * Time: 17:35
 */
    use yii\bootstrap\Html;
    use Dadata\Client;
    use app\models\User;
    use yii\db\Query;

    $this->title = Html::encode('Личный кабинет')
?>
<?= $modelUser->id . " - " . key(Yii::$app->authManager->getRolesByUser($modelUser->id))?>
<!--<?//= $modelUser->profile->getRolesToString()?>-->
    <br>
<?= $modelUser->username?>
    <br>
<?= $modelUser->email?>
    <br>
<?= $modelUser->created_at?>
    <br>
<?= ($modelUser->profile->name)?>
    <br>
<?= $modelUser->profile->surname?>
    <br>
<?= $modelUser->profile->patrinimic?>
    <br>
<?= $modelUser->profile->getSex()?>
<br>
<img src="<?=$modelUser->profile->urlPhoto?>" style="width: auto; height: 100px"/>
    <br>
<?= $modelUser->profile->bithday?>
<br>
<?php
    if($modelUser->profile->passport):
?>
<?= $modelUser->profile->passport->id?>
<br>
<?= $modelUser->profile->passport->date?>
<br>
<?= $modelUser->profile->passport->place?>
<br>
<?= $modelUser->profile->passport->country?>
<br>
<?php
    endif;
?>
<?php


?>