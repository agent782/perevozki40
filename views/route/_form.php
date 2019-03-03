<?php
/* @var $this yii\web\View */
/* @var $model app\models\Route */
/* @var $form yii\widgets\ActiveForm */
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\datetime\DateTimePicker;
/* @var $this yii\web\View */
/* @var $model app\models\Order */
$this->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU');
$this->registerJsFile('@web/js/route.js');
?>

<div id="route"  class="container" >
    <h4>Маршрут.</h4>
    <br>
    <?= $form->field($route, 'routeStart', ['inputOptions' => [
        'id'=>'rStart',
        'class' => 'points col-xs-12'
    ]])?>
    <br>
    <div id="hiddenRoutes">
        <ul>
            <?= Html::button(Html::icon('plus'), ['class' => 'addPoint btn-xs btn-info', 'title' => 'Добавить точку'])?>
            Промежуточные точки:
             <?php
                for($i = 1; $i < 9; $i++){
                    $attributePoint = 'route' . $i;
                    echo $form->field($route, $attributePoint, ['inputOptions' => [
                        'id'=>'r'.$i,
                        'class' => 'points col-xs-12',
                        'hidden' => (!$route->$attributePoint)? true : false
                    ]])->label(false);
                }
            ?>
        </ul>
    </div>
    <br>
    <?= $form->field($route, 'routeFinish', ['inputOptions' => ['id'=>'rFinish','class' => 'points col-xs-12']]);?>
    <br>
    <?= Html::button('Пересчитать', ['id' => 'but', 'class' => 'btn-sm btn-success'])?>

    <?= Html::button('Добавить промежуточную точку', ['class' => 'addPoint btn-sm btn-info'])?>

    <?= Html::button('Очистить', ['id' => 'clearAllPoint', 'class' => 'btn-sm btn-danger'])?>

    <?= Html::button('Скрыть/показать карту', ['class' => 'extremum-click btn-sm btn-warning'])?>
    <br>

    <h4><div>Приблизительный пробег*: <b id="len" class="h3"></b> км</div></h4>

    <?= $form->field($route, 'distance', ['inputOptions' => [
        'id'=>'lengthRoute',
    ]])->label(false)->hiddenInput();?>

    <br>
    <div id="map" class="extremum-slide" style="width: auto; height: 200px"></div>

</div>

