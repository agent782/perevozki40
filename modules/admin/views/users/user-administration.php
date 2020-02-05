<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 03.02.2020
 * Time: 14:44
 */
 use yii\bootstrap\Html;
?>

<?php
    $check = false;
    if($profile->canRole('client')){
            $name = 'vip-client';
            if($profile->canRole('vip-client')){
                $check = true;
            }
        }
?>
<?= ($profile->canRole('client'))?Html::checkbox('vip-client'):''?>
<?= ($profile->canRole('car_owner'))?Html::checkbox('vip-car-owner'):''?>

