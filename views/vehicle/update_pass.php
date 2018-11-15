<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 04.06.2018
 * Time: 11:13
 */
use yii\bootstrap\ActiveForm;

?>

        <?php

//        echo $form->field($model, 'tonnage', ['inputOptions' => [
//            'id' => 'tonnage',
//            'type' => 'tel',
//            'autofocus' => true
//        ]])->label('Общая грузоподъемность(пассажиры и груз).');
        echo $form->field($model, 'passengers', ['inputOptions' => [
            'id' => 'passengers',
            'type' => 'tel',
            'autofocus' => true,
            'onchange' => '
                UpdatePriceZones();
            '
        ]]);
//        echo $form->field($model, 'volume', ['inputOptions' => [
//            'id' => 'volume',
//            'type' => 'tel',
//            'autofocus' => true
//        ]]) ->label('Объем багажника.');
        ?>

