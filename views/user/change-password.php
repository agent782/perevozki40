<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 18.06.2019
 * Time: 16:38
 */
/* @var $ChangePasswordForm ChangePasswordForm;
 *
 */

use kartik\form\ActiveForm;
use app\models\ChangePasswordForm;
use yii\bootstrap\Html;

$this->title = 'Смена пароля.';
?>

<div class="container">
    <h3> <?= $this->title?> </h3>
    <?php
        $form = ActiveForm::begin([
//            'enableAjaxValidation' => true,
//            'validationUrl' => '/user/validate-user'
        ]);
    ?>
    <?= $form->field($ChangePasswordForm, 'old_pass')->passwordInput()?>
    <?= $form->field($ChangePasswordForm, 'new_pass')->passwordInput()?>
    <?= $form->field($ChangePasswordForm, 'new_pass_repeat')->passwordInput()?>
    <?= Html::submitButton()?>

    <?php
        $form::end();
    ?>
</div>
