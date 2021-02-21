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
use app\models\Vehicle;
?>
<?php



//$vehTypeId = $model->id_vehicle_type;
//$BodyTypes = ArrayHelper::map(BodyType::find()->where(['id_type_vehicle' => $vehTypeId])->asArray()->all(), 'id', 'body');
//unset($BodyTypes[0]);
//$LoadingTypes = ArrayHelper::map(LoadingType::find()->asArray()->all(), 'id', 'type');
//unset($LoadingTypes[0]);
//$imgBTs = ArrayHelper::map(BodyType::find()->asArray()->all(), 'id', 'image');
//$descBTs = ArrayHelper::map(BodyType::find()->asArray()->all(), 'id', 'description');
//$imgLTs = ArrayHelper::map(LoadingType::find()->asArray()->all(), 'id', 'image');
//$descLTs = ArrayHelper::map(LoadingType::find()->asArray()->all(), 'id', 'description');

;
//    var_dump($VehicleForm);
?>

        <div id="div_tonnage"
            <?=
            ($model->body_type === Vehicle::BODY_crane
                || $model->body_type === Vehicle::BODY_excavator
                || $model->body_type === Vehicle::BODY_excavator_loader)?'hidden':''
            ?>
        >
            <?php
            echo $form->field($model, 'tonnage', ['inputOptions' => [
                'id' => 'tonnage',
                'type' => 'text',
                'onchange' => 'UpdatePriceZones();'
            ]])->label('Максимальная грузоподъемность кузова:');
            ?>
        </div>
        <div id="sizes"
             <?=
             ($model->body_type !== Vehicle::BODY_manipulator)?'hidden':''
             ?>
        >
            <?php
            echo $form->field($model, 'length', ['inputOptions' => [
                    'id' => 'length',
                'type' => 'text',
                'onchange' => 'UpdatePriceZones();'
            ]]);
            echo $form->field($model, 'width', ['inputOptions' => [
                    'id' => 'width',
                'type' => 'text'
            ]]);
            //                echo $form->field($VehicleForm, 'height', ['inputOptions' => [
            ////                    'id' => 'height',
            //                    'type' => 'tel'
            //                ]]);
            ?>
        </div>
        <div id="div_volume"
            <?=
            ($model->body_type !== Vehicle::BODY_dump)?'hidden':''
            ?>
        >
            <?php
            echo $form->field($model, 'volume', ['inputOptions' => [
                'id' => 'volume',
                'type' => 'text',
                'onchange' => 'UpdatePriceZones();'
            ]]);
            ?>
        </div>
        <div id="sizes_spec"
            <?=
                ($model->body_type !== Vehicle::BODY_manipulator
                    && $model->body_type !== Vehicle::BODY_crane)?'hidden':''
            ?>
        >
            <?php
            echo $form->field($model, 'tonnage_spec', ['inputOptions' => [
                    'id' => 'tonnage_spec',
                'type' => 'text',
                'onchange' => 'UpdatePriceZones();'
            ]]);
            echo $form->field($model, 'length_spec', ['inputOptions' => [
                    'id' => 'length_spec',
                'type' => 'text',
                'onchange' => 'UpdatePriceZones();'
            ]]);
            ?>
        </div>
        <div id="div_volume_spec"
            <?=
            ($model->body_type !== Vehicle::BODY_excavator
                &&$model->body_type !== Vehicle::BODY_excavator_loader)?'hidden':''
            ?>
        >
            <?php
            echo $form->field($model, 'volume_spec', ['inputOptions' => [
                    'id' => 'volume_spec',
                'type' => 'text',
                'onchange' => 'UpdatePriceZones();'
            ]]);
            ?>
        </div>

