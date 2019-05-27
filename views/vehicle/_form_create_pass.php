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
<div class="row">
<?php
    $form = ActiveForm::begin([
//        'enableAjaxValidation' => true,
//        'validationUrl' => \yii\helpers\Url::to('validate-vehicle-form')
    ]);
    $vehTypeId = $VehicleForm->vehicleTypeId;
    $BodyTypes = ArrayHelper::map(BodyType::find()->where(['id_type_vehicle' => $vehTypeId])->asArray()->all(), 'id', 'body');
    unset($BodyTypes[0]);

$imgBTs = ArrayHelper::map(BodyType::find()->asArray()->all(), 'id', 'image');
$descBTs = ArrayHelper::map(BodyType::find()->asArray()->all(), 'id', 'description');

?>


    <div class="col-lg-4">
        <br>
        <?php

        echo $form->field($VehicleForm, 'tonnage', ['inputOptions' => [
            'id' => 'tonnage',
            'type' => 'tel',
            'autofocus' => true
        ]])->label('Общая грузоподъемность(пассажиры и груз). тонн(ы).');
        echo $form->field($VehicleForm, 'passengers', ['inputOptions' => [
            'id' => 'passengers',
            'type' => 'tel',
            'autofocus' => true
        ]]);
        echo $form->field($VehicleForm, 'volume', ['inputOptions' => [
            'id' => 'volume',
            'type' => 'tel',
            'autofocus' => true
        ]]) ->label('Объем багажника. м3');
        ?>
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

//                'id' => 'radioLongLenth'
            ]) . '<br>';
        ?>
    </div>
    <div class="col-lg-4">
        <?= $form->field($VehicleForm, 'description')->textarea()?>
    </div>
</div>


