<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 04.06.2018
 * Time: 11:13
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
        'enableAjaxValidation' => true,
        'validationUrl' => \yii\helpers\Url::to('validate-vehicle-form'),
//        'enableClientValidation' => true
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
?>
<div class="row">
    <div class="col-lg-4">
        <?php
        // Для валидации VehicleForm
        echo $form->field($VehicleForm, 'vehicleTypeId')->hiddenInput()->label(false);
        echo $form->field($VehicleForm, 'bodyTypeId')->radioList($BodyTypes, [
                'item' => function ($index, $label, $name, $checked, $value) use ($imgBTs, $descBTs) {
                    $return = '<label>';
                    $return .= '<input type="radio" name="' . $name . '"' . 'value="' . $value . '"' . ' >' . "\n";
                    $return .= '<i class="fa fa-square-o fa-2x"></i>' . "\n" .
                        '<i class="fa fa-check-square-o fa-2x"></i>' . "\n";
                    $return .= '<span>' . ucwords($label) . '</span>' . "\n";
                    $return .= '</label>';
                    $return .= ' ' . ShowMessageWidget::widget([
                            'helpMessage' => '<img style="width: 99%" src= /img/imgBodyTypies/' . $imgBTs[$value] . '> </img> <br> <br>' . $descBTs[$value],
                            'ToggleButton' => ['label' => '<img src="/img/icons/help-25.png">', 'class' => 'btn'],
                        ]);
                    $return .= '<br>';

                    return $return;
                },
                'onchange' => '
                    var body_type = $(this).find("input:checked").val();
                    $("#sizes, #tonnage, #sizes_spec, #volume_spec, #volume").find("input").val("");

                    if(body_type == 8){
                        $("#sizes, #tonnage, #sizes_spec").show();
//                        $("#volume_spec, #volume").find("input").val("0");
                        $("#volume_spec, #volume").hide();
                     }
                     if(body_type == 12){
                        $("#sizes_spec").show();
//                        $("#sizes, #tonnage, #volume_spec, #volume").find("input").val("0");
                        $("#sizes, #tonnage, #volume_spec, #volume").hide();

                     }
                     if(body_type == 13 || body_type == 15){
                        $("#volume_spec").show();
//                        $("#sizes, #tonnage, #sizes_spec, #volume").find("input").val("0");
                        $("#sizes, #tonnage, #sizes_spec, #volume").hide();

                     }
                     if(body_type == 14){
                        $("#volume, #tonnage").show();
//                        $("#sizes, #sizes_spec, #volume_spec").find("input").val(null);
                        $("#sizes, #sizes_spec, #volume_spec").hide();

                     }     
                '
            ]) . '<br>';
        ?>
    </div>
    <div class="col-lg-4">
        <br>
        <div id="tonnage" hidden>
            <?php
                echo $form->field($VehicleForm, 'tonnage', ['inputOptions' => [
//                    'id' => 'tonnage',
                    'type' => 'tel'
                ]])->label('Максимальная грузоподъемность кузова:');
            ?>
        </div>
        <div id="sizes" hidden>
            <?php
                echo $form->field($VehicleForm, 'length', ['inputOptions' => [
//                    'id' => 'length',
                    'type' => 'tel'
                ]]);
                echo $form->field($VehicleForm, 'width', ['inputOptions' => [
//                    'id' => 'width',
                    'type' => 'tel'
                ]]);
            ?>
        </div>
        <div id="volume" hidden>
            <?php
                echo $form->field($VehicleForm, 'volume', ['inputOptions' => [
//                    'id' => 'volume',
                    'type' => 'tel'
                ]]);
            ?>
        </div>
        <div id="sizes_spec" hidden>
            <?php
                echo $form->field($VehicleForm, 'tonnage_spec', ['inputOptions' => [
//                    'id' => 'tonnage_spec',
                    'type' => 'tel'
                ]]);
                echo $form->field($VehicleForm, 'length_spec', ['inputOptions' => [
//                    'id' => 'length_spec',
                    'type' => 'tel'
                ]]);
            ?>
        </div>
        <div id="volume_spec" hidden>
            <?php
                echo $form->field($VehicleForm, 'volume_spec', ['inputOptions' => [
//                    'id' => 'volume_spec',
                    'type' => 'tel'
                ]]);
            ?>
        </div>
    </div>
    <div class="col-lg-4">
        <?= $form->field($VehicleForm, 'description')->textarea()?>
    </div>
</div>


