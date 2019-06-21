<?php
namespace app\components;

use yii\bootstrap\Html;
//use yii\bootstrap\ActiveForm;
use app\components\myActiveForm;
use yii\helpers\ArrayHelper;
use kartik\rating\StarRating;

/* @var $this yii\web\View */
/* @var $modelOrder app\models\Order */

$this->title = 'Заказ автотранспорта.';
//$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
//var_dump($_POST);

?>
<div class="order-create">

    <h4><?= Html::encode($this->title) ?></h4>

    <?php
        $form = myActiveForm::begin();
    ?>
    <?= $form->field($modelOrder, 'id_vehicle_type')->radioList(
            ArrayHelper::map(\app\models\VehicleType::find()->asArray()->all(), 'id', 'type')
    )->label($modelOrder->getAttributeLabel('id_vehicle_type'), ['withTip' => true])?>

    <?=
    Html::a('Отмена', '/order/client', ['class' => 'btn btn-warning'])
    ?>

    <?= Html::submitButton('Далее', [
        'class' => 'btn btn-success',
        'name' => 'button',
        'value' => 'next1'
    ])?>

    <?php
        myActiveForm::end();
    ?>

</div>
