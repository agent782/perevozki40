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
        ->checkboxList($modelOrder->suitable_rates, [
            'item' => function ($index, $label, $name, $checked, $value) use ($route){
                $PriceZone = PriceZone::find()->where(['id' => $value])->one();
                $return = '<label>';
                $return .= '<input type="checkbox" name="' . $name . '"' . 'value="' . $value . '"' . ' >' . "\n";
                $return .= '<i class="fa fa-square-o fa-2x"></i>' . "\n" .
                    '<i class="fa fa-check-square-o fa-2x"></i>' . "\n";
//                $return .= '<span>' . ucwords($label) . '</span>' . "\n";
                $return .= ' &asymp; ' . $PriceZone->CostCalculation($route->distance);
                $return .= ' руб.* ';
                $return .= '</label>'
                    .  ShowMessageWidget::widget([
                        'helpMessage' => $PriceZone->printHtml(),
                        'header' => 'Тарифная зона ' . $value,
                        'ToggleButton' => ['label' => '<img src="/img/icons/help-25.png">', 'class' => 'btn'],
                    ])
                    . '</label><br>'
                ;

                return $return;
            }
        ]);
    ?>
    </div>
    <div class="col-lg-7">
        Информация о заказе.<br>
        <?= $modelOrder->getFullNewInfo(true,false, false)?>
    </div>
    <div class="col-lg-10">
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
