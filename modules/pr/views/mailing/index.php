<?php
/* @var $this yii\web\View
 * @var $MailingForm MailingForm
 */

use app\models\MailingForm;
use kartik\form\ActiveForm;
use yii\bootstrap\Html;

?>

<?php
    $form = ActiveForm::begin();
?>
<?= $form->field($MailingForm, 'to')->textarea()?>
<?= $form->field($MailingForm, 'subject')?>
<?= $form->field($MailingForm, 'text')->textarea()?>
<?= Html::submitButton('Начать рассылку', ['class' => 'btn btn-success'])?>

<?php
    $form::end();
?>

