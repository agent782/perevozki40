<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 03.04.2019
 * Time: 13:38
 */
?>
<label>Выберите подходящие тарифы </label>
<?= \yii\bootstrap\Html::activeCheckboxList($modelOrder,
    'selected_rates',
    $modelOrder->suitable_rates, [
        'id' => 'selected_rates',
        'encode' => false,
        'options' => [
            'label' => 111111111111
        ]
    ])
?>