<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 17.10.2018
 * Time: 13:12
 */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;use yii\jui\ProgressBar;


    $this->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU');
    $this->registerJsFile('@web/js/route.js');
?>

<?php
$form = ActiveForm::begin();
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
            <?= $form->field($route, 'route1', ['inputOptions' => ['id'=>'r1','class' => 'points col-xs-12']])->label('Промежуточные точки')->hiddenInput();?>
            <?= $form->field($route, 'route2', ['inputOptions' => ['id'=>'r2','class' => 'points col-xs-12']])->label(false)->hiddenInput();?>
            <?= $form->field($route, 'route3', ['inputOptions' => ['id'=>'r3','class' => 'points col-xs-12']])->label(false)->hiddenInput();?>
            <?= $form->field($route, 'route4', ['inputOptions' => ['id'=>'r4','class' => 'points col-xs-12']])->label(false)->hiddenInput();?>
            <?= $form->field($route, 'route5', ['inputOptions' => ['id'=>'r5','class' => 'points col-xs-12']])->label(false)->hiddenInput();?>
            <?= $form->field($route, 'route6', ['inputOptions' => ['id'=>'r6','class' => 'points col-xs-12']])->label(false)->hiddenInput();?>
            <?= $form->field($route, 'route7', ['inputOptions' => ['id'=>'r7','class' => 'points col-xs-12']])->label(false)->hiddenInput();?>
            <?= $form->field($route, 'route8', ['inputOptions' => ['id'=>'r8','class' => 'points col-xs-12']])->label(false)->hiddenInput();?>
        </ul>
    </div>
    <br>
    <?= $form->field($route, 'routeFinish', ['inputOptions' => ['id'=>'rFinish','class' => 'points col-xs-12']]);?>
    <br>
    <?= Html::button('Пересчитать', ['id' => 'but'])?>

    <?= Html::button('Добавить промежуточную точку', ['id' => 'addPoint'])?>

    <?= Html::button('Очистить', ['id' => 'clearAllPoint'])?>

    <?= Html::button('Скрыть/показать карту', ['class' => 'extremum-click'])?>
    <br>

    <h4><div>Приблизительный пробег*: <b id="len" class="h3"></b> км</div></h4>

        <?= $form->field($route, 'distance', ['inputOptions' => [
        'id'=>'lengthRoute',
    ]])->label(false)->hiddenInput();?>

        <br>
    <div id="map" class="extremum-slide" style="width: auto; height: 200px"></div>

    <br>
    <div class="col-lg-12">
        <?=
        Html::a('Отмена', '/order', ['class' => 'btn btn-warning'])
        ?>

        <?= Html::submitButton('Далее', [
            'class' => 'btn btn-success',
            'name' => 'button',
            'value' => 'next4'
        ])?>
    </div>
</div>

<?php
    ActiveForm::end();
?>

<script type="text/javascript">
    //Нет submit формы при нажатии enter
    $(function () {
        $('.points').keypress(function (event) {
            if (event.which == '13') {
                event.preventDefault();
            }
        })
    });
</script>
