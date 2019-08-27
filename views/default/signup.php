<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 08.01.2018
 * Time: 12:02
 */
//    use yii\bootstrap\ActiveForm;
    use yii\bootstrap\ActiveForm;
    use yii\bootstrap\Html;
    $this->title = 'Регистрация (1/4)';
?>

<h4><?=$this->title?></h4>

<?php
//var_dump(Yii::$app->session->get('modelProfile')->name);
//echo '<br>';
//    var_dump($modelProfile);
    $form = ActiveForm::begin();
?>
<?= $form->field($modelProfile, 'surname')->label('Фамилия')?>
<?= $form->field($modelProfile, 'name')->label('Имя')?>
<?= $form->field($modelProfile, 'patrinimic')->label('Отчество')?>
<?= $form->field($modelProfile, 'sex')->dropDownList(['Мужской', 'Женский'])->label('Пол')?>
<?= Html::submitButton('Далее', [
    'class' => 'btn btn-primary',
    'name' => 'button',
    'value' => 'signup1'
])?>
<?php
    ActiveForm::end();
?>
