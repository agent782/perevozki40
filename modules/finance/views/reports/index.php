<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
$this->title = 'Отчеты'
?>
<h3><?= $this->title?></h3>

<?php
    $form = ActiveForm::begin();
?>

<?= $form->field($model, 'date1')?>
<?= $form->field($model, 'date2')?>
<?= Html::submitButton('Выполнить', ['class' => 'btn btn-info'])?>
<?php
    $form::end();
?>
