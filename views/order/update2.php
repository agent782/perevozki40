<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 05.03.2019
 * Time: 11:58*
 */
    use yii\bootstrap\ActiveForm;
    use app\models\PriceZone;
    use app\components\widgets\ShowMessageWidget;
    use yii\bootstrap\Html;
/* @var $modelOrder \app\models\Order*/
    $this->title = 'Изменение заказа №' . $modelOrder->id;
?>

<div class="container">
    <h3><?= Html::encode($this->title) ?></h3>

    <?php
    $form = ActiveForm::begin();
    ?>
    <div class="col-lg-5">
    <?= $form->field($modelOrder, 'selected_rates')->label('Выберите подходящие тарифы *.')
        ->checkboxList($modelOrder->suitable_rates, ['encode' => false]);
    ?>
    </div>
    <div class="col-lg-7">
        Информация о заказе.<br>
        <?= $modelOrder->getFullNewInfo(true,false, false)?>
    </div>
    <div class="col-lg-10">
        <?=
        Html::submitButton('Назад',
            [
                'class' => 'btn btn-warning',
                'name' => 'button',
                'value' => 'back'
            ]);
//            Html::a('Назад', ['/order/update', 'id_order' => $modelOrder->id], ['class' => 'btn btn-warning'])
        ?>
        <?= Html::submitButton('Подтвердить',
            [
                'class' => 'btn btn-success',
                'name' => 'button',
                'value' => 'update2'
            ])?>
    </div>
    <?php
        ActiveForm::end();
    ?>
</div>