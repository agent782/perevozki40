<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 09.09.2019
 * Time: 8:49
 */
/* @var $this \yii\web\View
 */
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = Html::encode('Регистрация и завершение повторного заказа.');
?>

<h4><?=$this->title?></h4>

<br><br>

<?php
    $form = ActiveForm::begin();
?>
<?= $form->field($modelOrder, 'id_vehicle')->radioList($vehicles, [
    'encode' => false
])->label('Выберите ТС:')?>
<?= $form->field($modelOrder, 'id_driver')->radioList($driversArr)
    ->label('Выберите водителя:')
?>
<?= Html::submitButton('Далее', [
    'class' => 'btn btn-success',
    'name' => 'button',
    'value' => 'next1'
])?>
<?php
    $form::end();
?>
