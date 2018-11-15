<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;

?>

    <?php
    $formOrder = new ActiveForm();
    $formOrder::begin([
        'id' => 'form-order',
        'options' => [
            'class' => 'form-horizontal col-lg-11',
            'title' => 'ORDER',
            ]
    ]);
    ?>
    <?php

    ?>

    <?= $formOrder->field($order, 'date_start',
        ['inputOptions'=>[
            'id'=>'date_start',
            ]
        ]) -> widget(DatePicker::className()); ?>
    <?= $formOrder->field($order, 'id_vehicle'); ?>
    <?= $formOrder->field($order, 'weight'); ?>
    <?= $formOrder->field($order, 'v'); ?>

    <?php $formOrder::end();?>




<script>
        $(document).ready(function () {
            $('#order-date_start-kvdate').on('change', function () {
                alert ('TEST');
            })
        })

    </script>