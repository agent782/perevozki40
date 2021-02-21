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
            'type' => 'text',
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
            'type' => 'text'
        ]])->input('text', [
            'onchange' => 'setVolume(); UpdatePriceZones();',
            'type' => 'text'
        ]);
        echo $form->field($model, 'width', ['inputOptions' => [
            'id' => 'width',
            'type' => 'text'
        ]])->input('text', [
            'onchange' => 'setVolume();UpdatePriceZones();'
        ]);
        echo $form->field($model, 'height', ['inputOptions' => [
            'id' => 'height',
            'type' => 'text'
        ]])->input('text', [
            'onchange' => 'setVolume();UpdatePriceZones();',
        ]);
        echo $form->field($model, 'volume', ['inputOptions' => [
            'id' => 'volume',
            'type' => 'text',
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
            var v = l*w*h;
            $("#volume").val(v.toFixed(2));
        } else $("#volume").val("");
    };
</script>
