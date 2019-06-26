<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 11.07.2018
 * Time: 10:04
 */
//$model = $session->get('model');
    use yii\bootstrap\ActiveForm;
    use yii\bootstrap\Html;
    use app\models\Vehicle;
    use yii\helpers\Url;

?>
<div class="container">
<?php
    $form = ActiveForm::begin([

        'enableAjaxValidation' => true,
        'validationUrl' => \yii\helpers\Url::to('validate-price-zone'),

    ]);

?>
<?php //var_dump($model->body_types)?>
<div class="col-lg-6">
    <?php
        switch ($model->veh_type){
            case Vehicle::TYPE_TRUCK :

                echo $form->field($model, 'longlength')->radioList(['Нет', 'Да'], [
                    'value' => 0,
                    'id' => 'radioLonglength'
                    ]);
                ?>
                <div id="NOTlonglength">
                    <?php
                    echo $form->field($model, 'tonnage_min', ['inputOptions' => ['id' => 'tonnage_min']]);
                    echo $form->field($model, 'tonnage_max', ['inputOptions' => ['id' => 'tonnage_max']]);
                    echo $form->field($model, 'length_min', ['inputOptions' => ['id' => 'length_min']]);
                    echo $form->field($model, 'length_max', ['inputOptions' => ['id' => 'length_max']]);
                    echo $form->field($model, 'volume_min', ['inputOptions' => ['id' => 'volume_min']]);
                    echo $form->field($model, 'volume_max', ['inputOptions' => ['id' => 'volume_max']]);
                    ?>
                </div>

                <?= $form->field($model, 'passengers');?>

                <?php

                break;
            case Vehicle::TYPE_PASSENGER :
//                echo $form->field($model, 'id');
                echo $form->field($model, 'passengers');
                break;
            case Vehicle::TYPE_SPEC :
                switch ($model->body_types) {
                    case Vehicle::BODY_manipulator:
                        echo $form->field($model, 'tonnage_min');
                        echo $form->field($model, 'tonnage_max');
                        echo $form->field($model, 'length_min');
                        echo $form->field($model, 'length_max');
                        echo $form->field($model, 'tonnage_spec_min');
                        echo $form->field($model, 'tonnage_spec_max');
                        echo $form->field($model, 'length_spec_min');
                        echo $form->field($model, 'length_spec_max');
                        break;
                    case Vehicle::BODY_crane:
                        echo $form->field($model, 'tonnage_spec_min');
                        echo $form->field($model, 'tonnage_spec_max');
                        echo $form->field($model, 'length_spec_min');
                        echo $form->field($model, 'length_spec_max');
                        break;
                    case Vehicle::BODY_dump:
                        echo $form->field($model, 'tonnage_min');
                        echo $form->field($model, 'tonnage_max');
                        echo $form->field($model, 'volume_min');
                        echo $form->field($model, 'volume_max');
                        break;
                    case Vehicle::BODY_excavator:
                        echo $form->field($model, 'volume_spec');
                        break;
                    case Vehicle::BODY_excavator_loader:
    //                        $model->scenario = \app\models\PriceZone::SCENARIO_EXCAVATOR_LOADER;
                        echo $form->field($model, 'volume_spec');
                        break;
                }
                break;
            default:
                break;
        }
    ?>
</div>
<div class="col-lg-6">
    <?= $form->field($model, 'r_km')?>
    <?= $form->field($model, 'h_loading')?>
    <?= $form->field($model, 'r_loading')?>
    <?= $form->field($model, 'min_price')?>
    <?= $form->field($model, 'r_h')?>
    <?= $form->field($model, 'min_r_10')?>
    <?= $form->field($model, 'min_r_20')?>
    <?= $form->field($model, 'min_r_30')?>
    <?= $form->field($model, 'min_r_40')?>
    <?= $form->field($model, 'min_r_50')?>
    <?php
        if($model->veh_type == Vehicle::TYPE_TRUCK) {
            echo $form->field($model, 'remove_awning');
        }
    ?>
</div>
<div class="col-lg-12">
<?php
    echo Html::a('Отмена', Url::to('/price-zone'), [
        'class' => 'btn btn-info',
    ]).' ';
    echo Html::submitButton('Добавить тариф', [
    'id' => 'next3',
    'name' => 'button',
    'value' => 'next3',
    'class' => 'btn btn-success'
    ]);

    ActiveForm::end();
?>
</div>
</div>
<style>
    input[type=text]{
        width:  100px;
    }
    label{
        font-size: small;
    }
</style>