<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 31.01.2018
 * Time: 14:38
 */
//$this->registerCssFile("https://cdn.jsdelivr.net/npm/suggestions-jquery@17.10.1/dist/css/suggestions.min.css");
//$this->registerJsFile("https://cdn.jsdelivr.net/npm/suggestions-jquery@17.10.1/dist/js/jquery.suggestions.min.js");
//$this->registerJsFile('/js/jquery-dateFormat.js');
//$this->registerJsFile('/js/addCompany.js');
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\widgets\MaskedInput;

$this->title = Html::encode('Регистрация юридического лица.');
?>
<div class="container">
    <h3><?=$this->title?></h3>
</div>
<p>
    <?= Html::a('Назад', [\yii\helpers\Url::previous()], ['class' => 'btn btn-success']) ?>
</p>
<br>
<div class="company-form">

    <?php
    $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'validationUrl' => \yii\helpers\Url::to(['/company/validate-add-company']),
        'fieldConfig' => [
//            'template' => "{label}<br>{input}<br>{error}",
            'labelOptions' => ['class' => 'col-lg-12 control-label'],
        ],
    ]);
    ?>
<?= $this->render('_form', [
    'modelCompany' => $modelCompany,
    'XcompanyXprofile' => $XcompanyXprofile,
        'form' => $form
]) ?>
    <div class="form-group">
        <?= Html::submitButton($modelCompany->isNewRecord ? 'Добавить' : 'Сохранить',
            [
                'class' => $modelCompany->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
            ]) ?>
    </div>
    <?php
    ActiveForm::end();
    ?>
</div>

