<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 04.06.2018
 * Time: 10:58
 */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use app\models\BodyType;
use app\models\LoadingType;
use app\components\widgets\ShowMessageWidget;
?>

<?php
$form = ActiveForm::begin([
//    'enableAjaxValidation' => true,
//    'validationUrl' => \yii\helpers\Url::to('validate-vehicle-form')
]);

$vehTypeId = $VehicleForm->vehicleTypeId;
$BodyTypes = ArrayHelper::map(BodyType::find()->where(['id_type_vehicle' => $vehTypeId])->asArray()->all(), 'id', 'body');
unset($BodyTypes[0]);
$LoadingTypes = ArrayHelper::map(LoadingType::find()->asArray()->all(), 'id', 'type');
unset($LoadingTypes[0]);
$imgBTs = ArrayHelper::map(BodyType::find()->asArray()->all(), 'id', 'image');
$descBTs = ArrayHelper::map(BodyType::find()->asArray()->all(), 'id', 'description');
$imgLTs = ArrayHelper::map(LoadingType::find()->asArray()->all(), 'id', 'image');
$descLTs = ArrayHelper::map(LoadingType::find()->asArray()->all(), 'id', 'description');

;
//    var_dump($VehicleForm);
?>

<div class="row">
        <div class="col-lg-4">
            <br>
            <?php

                    echo $form->field($VehicleForm, 'tonnage', ['inputOptions' => [
                        'id' => 'tonnage',
                        'type' => 'tel',
                        'autofocus' => true
                    ]]);
                    echo $form->field($VehicleForm, 'passengers', ['inputOptions' => [
                        'id' => 'passengers',
                        'type' => 'tel'
                    ]]);
                    echo $form->field($VehicleForm, 'length', ['inputOptions' => [
                        'id' => 'length',
                        'type' => 'tel'
                    ]])->input('number', [
                        'onchange' => 'setVolume();',
                        'type' => 'tel'
                    ]);
                    echo $form->field($VehicleForm, 'width', ['inputOptions' => [
                        'id' => 'width',
                        'type' => 'tel'
                    ]])->input('number', [
                        'onchange' => 'setVolume();'
                    ]);
                    echo $form->field($VehicleForm, 'height', ['inputOptions' => [
                        'id' => 'height',
                        'type' => 'tel'
                    ]])->input('number', [
                        'onchange' => 'setVolume();',
                    ]);
                    echo $form->field($VehicleForm, 'volume', ['inputOptions' => [
                        'id' => 'volume',
                        'type' => 'tel'
                    ]]);
                    echo $form->field($VehicleForm, 'ep', ['inputOptions' => [
                        'type' => 'tel'
                    ]]);
                    echo $form->field($VehicleForm, 'rp', ['inputOptions' => [
                        'type' => 'tel'
                    ]]);
                    echo $form->field($VehicleForm, 'lp', ['inputOptions' => [
                        'type' => 'tel'
                    ]]);


            echo $form->field($VehicleForm, 'longlength')->radioList(['Нет', 'Да'], [
                        'value' => 0,
                        'id' => 'radioLonglength',
//                        'onchange' => '
//                                        if($(this).find("input:checked").val() == 1) $("#longlength").show();
//                                        else {
//                                            $("#longlength").hide();
//                                            $("#tonnage_long").val("");
//                                            $("#length_long").val("");
//                                        }
//                                    '
                    ]);
            ?>
<!--<div id="longlength" hidden>-->
<!--    --><?php
//    echo $form->field($VehicleForm, 'tonnage_long', ['inputOptions' => ['id' => 'tonnage_long']]);
//    echo $form->field($VehicleForm, 'length_long', ['inputOptions' => ['id' => 'length_long']]);
//    ?>
<!--</div>-->
</div>
<br>
<div class="col-lg-4">
    <?php
    echo $form->field($VehicleForm, 'bodyTypeId')->radioList($BodyTypes, [
            'item' => function ($index, $label, $name, $checked, $value) use ($imgBTs, $descBTs) {
                $return = '<label>';
                $return .= '<input type="radio" name="' . $name . '"' . 'value="' . $value . '"' . ' >' . "\n";
                $return .= '<i class="fa fa-square-o fa-2x"></i>' . "\n" .
                    '<i class="fa fa-check-square-o fa-2x"></i>' . "\n";
                $return .= '<span>' . ucwords($label) . '</span>' . "\n";
                $return .= '</label>';
                $return .= ' ' . ShowMessageWidget::widget([
                        'helpMessage' => '<img src= /img/imgBodyTypes/' . $imgBTs[$value] . '> </img> <br> <br>' . $descBTs[$value],
                        'ToggleButton' => ['label' => '<img src="/img/icons/help-25.png">', 'class' => 'btn'],
                    ]);
                $return .= '<br>';

                return $return;
            },
        ]) . '<br>';
    echo $form->field($VehicleForm, 'loadingTypeIds[]')->checkboxList($LoadingTypes, [
            'item' => function ($index, $label, $name, $checked, $value) use ($imgLTs, $descLTs) {
                $return = '<label>';
                $return .= '<input type="checkbox" name="' . $name . '"' . 'value="' . $value . '"' . ' >' . "\n";
                $return .= '<i class="fa fa-square-o fa-2x"></i>' . "\n" .
                    '<i class="fa fa-check-square-o fa-2x"></i>' . "\n";
                $return .= '<span>' . ucwords($label) . '</span>' . "\n";
                $return .= '</label>';
                $return .= ' ' . ShowMessageWidget::widget([
                        'helpMessage' => '<img src= /img/imgLoadingTypes/' . $imgLTs[$value] . '> </img> <br> <br>' . $descLTs[$value],
                        'ToggleButton' => ['label' => '<img src="/img/icons/help-25.png">', 'class' => 'btn'],
                    ]);
                $return .= '<br>';

                return $return;
            }
        ]) . '<br>';
    ?>
</div>

<div class="col-lg-4">
    <?= $form->field($VehicleForm, 'description')->textarea()?>

</div>
</div>

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