<?php

use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Order */

$this->title = 'Заказ автотранспорта.';
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
//var_dump($_POST);

?>
<div class="order-create">

    <h4><?= Html::encode($this->title) ?></h4>

    <?php
        $form = ActiveForm::begin();
    ?>

    <?= $form->field($modelOrder, 'id_vehicle_type')->radioList(
            ArrayHelper::map(\app\models\VehicleType::find()->asArray()->all(), 'id', 'type')
    )?>

    <?=
    Html::a('Отмена', '/order', ['class' => 'btn btn-warning'])
    ?>

    <?= Html::submitButton('Далее', [
        'class' => 'btn btn-success',
        'name' => 'button',
        'value' => 'next1'
    ])?>

    <?php
        ActiveForm::end();
    ?>

</div>
