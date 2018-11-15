<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Vehicle;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;



/* @var $this yii\web\View */
/* @var $model app\models\PriceZone */

$this->title = 'Создание тарифной зоны.';
$this->params['breadcrumbs'][] = ['label' => 'Price Zones', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="price-zone-create">

    <h4><?= Html::encode($this->title) ?></h4>

    <?php
        $form = ActiveForm::begin(['options' => ['data' => ['pjax' => true]],]);
        $VehTypies = \yii\helpers\ArrayHelper::map(
            \app\models\VehicleType::find()->asArray()->all(),
            'id', 'type'
        );
        echo $form->field($model, 'veh_type')->radioList($VehTypies, [
            'id' => 'radioVehTypies',
//            'name' => 'button',
//            'value' => 'radioVehTypies',
            'onchange' => '
                    $("#radioVehType").click();
                '
        ]);
        echo Html::a('Назад', Url::to('/price-zone'), [
            'class' => 'btn btn-info',
        ]).' ';
        echo Html::submitButton('Далее', [
            'id' => 'next',
            'name' => 'button',
            'value' => 'next',
            'class' => 'btn btn-success'
        ]);
    ?>

<!--    --><?php
//        switch ($model->veh_type){
//            case Vehicle::TYPE_TRUCK:
//                echo 111111;
//                break;
//            case Vehicle::TYPE_PASSENGER:
//                echo 222222;
//                break;
//            case Vehicle::TYPE_SPEC:
//                echo $form->field($model, 'body_types')->radioList(
//                        ArrayHelper::map(
//                                \app\models\BodyType::find()->where(['id_type_vehicle' => $model->veh_type])
//                                ->asArray()->all(),
//                                'id',
//                                'body'
//                        ),
//                        [
//                            'onchange' => '
//                                $("#radioBodyType").click();
//                            '
//                ]
//                );
//                switch ($model->body_types){
//                    case Vehicle::BODY_manipulator:
//                        echo $form->field($model, 'tonnage_min');
//                        echo $form->field($model, 'tonnage_max');
//                        echo $form->field($model, 'length_min');
//                        echo $form->field($model, 'length_max');
//                        echo $form->field($model, 'tonnage_spec_min');
//                        echo $form->field($model,'tonnage_spec_max');
//                        echo $form->field($model, 'length_spec_min');
//                        echo $form->field($model, 'length_spec_max');
//
//                        break;
//                    case Vehicle::BODY_crane:
//                        echo $form->field($model, 'tonnage_spec_min');
//                        echo $form->field($model,'tonnage_spec_max');
//                        echo $form->field($model, 'length_spec_min');
//                        echo $form->field($model, 'length_spec_max');
//
//                        break;
//                    case Vehicle::BODY_dump:
//                        echo $form->field($model, 'tonnage_min');
//                        echo $form->field($model, 'tonnage_max');
//                        echo $form->field($model, 'volume_min');
//                        echo $form->field($model, 'volume_max');
//
//                        break;
//                    case Vehicle::BODY_excavator:
//                        echo $form->field($model, 'volume_spec');
//
//                        break;
//                    case Vehicle::BODY_excavator_loader:
////                        $model->scenario = \app\models\PriceZone::SCENARIO_EXCAVATOR_LOADER;
//                        echo $form->field($model, 'volume_spec');
//
//                        break;
//                    default:
////                        echo var_dump($model);
//                        echo 'Выберите тип кузова';
//                        break;
//                }
//                break;
//            default:
//                echo 'Выберите тип транспорта.';
//        }
//        echo Html::submitButton('BUTTON', [
//                'id' => 'radioVehType',
//            'hidden' =>true
//        ]). '<br>';
//    echo Html::submitButton('BUTTON', [
//            'id' => 'radioBodyType',
//            'name' => 'button',
//            'value' => 'radioBodyType',
//            'hidden' =>true
//        ]). '<br>';
//        if($model->veh_type){
//            echo Html::submitButton('Далее', [
//                'id' => 'next',
//                'name' => 'button',
//                'value' => 'next'
//            ]);
//        }
//    ?>
    <?php
        ActiveForm::end();
    ?>

</div>


