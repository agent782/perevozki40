<?php

use yii\helpers\ArrayHelper;
use app\models\BodyType;
use app\models\LoadingType;
use app\components\widgets\ShowMessageWidget;

$vehTypeId = $model->id_vehicle_type;
?>

<?php

        echo $form->field($model, 'tonnage', ['inputOptions' => [
            'id' => 'tonnage',
            'type' => 'tel',
            'autofocus' => true,
            'onchange' => '
                UpdatePriceZones();
            '
        ]]);
        echo $form->field($model, 'passengers', ['inputOptions' => [
            'id' => 'passengers',
            'type' => 'tel'
        ]]);
        echo $form->field($model, 'length', ['inputOptions' => [
            'id' => 'length',
            'type' => 'tel'
        ]])->input('number', [
            'onchange' => 'setVolume(); UpdatePriceZones();',
            'type' => 'tel'
        ]);
        echo $form->field($model, 'width', ['inputOptions' => [
            'id' => 'width',
            'type' => 'tel'
        ]])->input('number', [
            'onchange' => 'setVolume();UpdatePriceZones();'
        ]);
        echo $form->field($model, 'height', ['inputOptions' => [
            'id' => 'height',
            'type' => 'tel'
        ]])->input('number', [
            'onchange' => 'setVolume();UpdatePriceZones();',
        ]);
        echo $form->field($model, 'volume', ['inputOptions' => [
            'id' => 'volume',
            'type' => 'tel',
            'onchange' => '
                UpdatePriceZones();
            '
        ]]);
        echo $form->field($model, 'ep', ['inputOptions' => [
            'type' => 'tel'
        ]]);
        echo $form->field($model, 'rp', ['inputOptions' => [
            'type' => 'tel'
        ]]);
        echo $form->field($model, 'lp', ['inputOptions' => [
            'type' => 'tel'
        ]]);


        echo $form->field($model, 'longlength')->radioList(['Нет', 'Да'], [
//            'value' => 0,
            'id' => 'longlength',
            'onchange' => '
                UpdatePriceZones();
            '
        ]);
        ?>

<script>
    function setVolume() {
        var l = $("#length").val();
        var w = $("#width").val();
        var h = $("#height").val();
        if(l && w && h){
            $("#volume").val(l*w*h);
        } else $("#volume").val("");
    };
</script>
