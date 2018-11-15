<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 18.01.2018
 * Time: 10:26
 */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

$this->title = Html::encode('Редактирование профиля');
?>
<p>
    <?= Html::a('Назад', [\yii\helpers\Url::previous()], ['class' => 'btn btn-success']) ?>
</p>
<?php
//var_dump($profile);
$form = ActiveForm::begin([
//    'enableAjaxValidation' => true,
//    'validationUrl' => \yii\helpers\Url::to(['/default/validate-phone/validate-phone']), // Добавить URL валидации
]);
echo $form->field($model, 'username')->textInput(['value' => $model->username]);
echo $form->field($profile, 'surname')->textInput(['value' => $profile->surname]);
echo $form->field($profile, 'name')->textInput(['value' => $profile->name]);
echo $form->field($profile, 'patrinimic')->textInput(['value' => $profile->patrinimic]);
echo Html::submitButton('Сохранить',['class' => 'btn btn-success']);
//echo Html::resetButton('Отменить', ['class' => 'btn btn-warning']);
ActiveForm::end();