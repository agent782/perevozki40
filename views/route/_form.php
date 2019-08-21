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
$this->registerJsFile('@web/js/route.js');
?>

<div id="route">
    <h4>Маршрут.</h4>
    <p class="border-warning">Перед продолжением нажмите кнопку "Пересчитать"! Для определения расстояния.</p>
    <br>
    <?= $form->field($route, 'routeStart', ['inputOptions' => [
        'id'=>'rStart',
        'class' => 'points col-xs-12'
    ]])?>
    <br>
    <div id="hiddenRoutes">
        <ul>
            <?= Html::button(Html::icon('plus'), ['class' => 'addPoint btn-xs btn-info', 'title' => 'Добавить точку'])?>
            Промежуточные точки (не более 8-ми):
             <?php
                for($i = 1; $i < 9; $i++){
                    $attributePoint = 'route' . $i;
                    echo $form->field($route, $attributePoint, ['inputOptions' => [
                        'id'=>'r'.$i,
                        'class' => 'points col-xs-12',
                        'hidden' => (!$route->$attributePoint)? true : false,
                        'style' => 'margin: 5px'
                    ]])->label(false);
                }
            ?>
        </ul>
    </div>

    <?= $form->field($route, 'routeFinish', ['inputOptions' => ['id'=>'rFinish','class' => 'points col-xs-12']]);?>
    <br><br>
    <?= Html::button('Пересчитать', ['id' => 'but', 'class' => 'btn-sm btn-success'])?>

    <?= Html::button('Добавить промежуточную точку', ['class' => 'addPoint btn-sm btn-info'])?>

    <?= Html::button('Очистить', ['id' => 'clearAllPoint', 'class' => 'btn-sm btn-danger'])?>

    <?= Html::button('Скрыть/показать карту', ['class' => 'extremum-click btn-sm btn-warning'])?>
    <br>

    <h4><div>Приблизительный пробег*: <b id="len" class="h3"></b> км</div></h4>
    <comment style="color: red">
        Обращаем Ваше внимание! Расстояние вычисляется автоматически сервисом Яндекс Карт. Особенно для Москвы, результат не всегда корректен!. Так как
        Яндекс считает оптимальный маршрут для легковых автомобилей (через центр, ТТК и т. д.), тогда как грузовой транспорт едет по МКАД.
        Таким образом, фактическое расстояние может отличаться примерно на 10%, а следовательно и конечная цена заказа. Как только
        сервис Яндекс карт позволит учитывать типы транспорта при рассчетах, мы исправим это "неудобство".
    </comment>
    <?= $form->field($route, 'distance', ['inputOptions' => [
        'id'=>'lengthRoute',
    ]])->label(false)->hiddenInput();?>

    <br>
    <div id="map" class="extremum-slide" style="width: auto; height: 200px"></div>

</div>

