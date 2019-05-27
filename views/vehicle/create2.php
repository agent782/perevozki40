<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 29.05.2018
 * Time: 13:32
 */
    use yii\bootstrap\ActiveForm;
    use yii\bootstrap\Html;
    use yii\helpers\ArrayHelper;
    use app\models\BodyType;
    use app\models\LoadingType;
    use app\components\widgets\ShowMessageWidget;
    use yii\helpers\Url;
$this->registerJsFile('/js/signup.js');

$this->title = 'Новое транспортное средство.';
//$this->params['breadcrumbs'][] = ['label' => 'Vehicles', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;

?>
<div class="vehicle-create2 row" >

    <h4><?= Html::encode($this->title) ?></h4>
    <br>


    <?php
        $vehTypeId = $VehicleForm->vehicleTypeId;
        switch ($vehTypeId){
            case 1:
                echo $this->render('_form_create_truck', [
                    'modelVehicle' => $modelVehicle,
                    'VehicleForm' => $VehicleForm
                ]);
                break;
            case 2:
                echo $this->render('_form_create_pass', [
                    'modelVehicle' => $modelVehicle,
                    'VehicleForm' => $VehicleForm
                ]);
                break;
            case 3:
                echo $this->render('_form_create_spec', [
                    'modelVehicle' => $modelVehicle,
                    'VehicleForm' => $VehicleForm
                ]);
                break;
            default:
                break;
        }

    ?>
    <div class="col-lg-12 form-group">
        <?= Html::a('Назад', Url::to(['/vehicle/create','id_user' => Yii::$app->user->id]), ['class' => 'btn btn-warning'])?>
        <?= Html::submitButton('Далее', ['class' => 'btn btn-success', 'name' => 'button', 'value' => 'create_next2']) ?>
        <?php
        ActiveForm::end();
        ?>
    </div>
</div>