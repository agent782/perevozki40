<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 17.10.2018
 * Time: 13:12
 */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;use yii\jui\ProgressBar;
?>
<div class="container">
<?php
$form = ActiveForm::begin();
?>
<?= $this->render('/route/_form', ['route' => $route, 'form' => $form])?>


    <br>
    <div class="col-lg-12">
        <?=
        Html::a('Отмена', '/order/client', ['class' => 'btn btn-warning'])
        ?>

        <?= Html::submitButton('Далее', [
            'class' => 'btn btn-success',
            'name' => 'button',
            'value' => 'next4'
        ])?>
    </div>

<?php
    ActiveForm::end();
?>
</div>
<script type="text/javascript">
    //Нет submit формы при нажатии enter
    $(function () {
        $('.points').keypress(function (event) {
            if (event.which == '13') {
                event.preventDefault();
            }
        })
    });
</script>
