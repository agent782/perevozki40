<?php

use yii\bootstrap\ActiveForm;
Use yii\helpers\Url;
use yii\bootstrap\Html;
/* @var $this yii\web\View */
/* @var $model app\models\Company */

$this->title = 'Редактирование юл. лица: ' . $modelCompany->name;



?>

    <h3><?= Html::encode($this->title) ?></h3>
<p>
    <?= Html::a('Назад', [Url::previous()], ['class' => 'btn btn-success']) ?>
</p>
<br>
<div class="company-form">

    <?php
    $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
//        'enableClientValidation' => true,
        'validationUrl' => Url::to(['/company/validate-add-company']),
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
        <?= Html::submitButton($modelCompany->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $modelCompany->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php
    ActiveForm::end();
    ?>
</div>

</div>

