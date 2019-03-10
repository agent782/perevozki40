<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Tip */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tip-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'model')->dropDownList(\app\components\functions\functions::getModelsNames(), [
        'prompt' => 'Выберите модель',
        'id' => 'model_name',
        'onchange' => 'getAttributesForModel()'
    ])?>

    <?= $form->field($model, 'attribute')->dropDownList([], [
        'id' => 'attributes',
    ]) ?>

    <?= $form->field($model, 'description')->textarea() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
    function getAttributesForModel() {
        var model_name = $('#model_name').find('option:selected').text();
        $.ajax({
            type:'POST',
            url:'/tip/ajax-get-attributes-for-model',
            data:{
                model_name:model_name,
            },
            dataType:'json',
            success:function (data) {
                $('#attributes').empty();
                for ( var i in data){
//                    alert (data[i]);
                    $('#attributes').append(
                        '<option value="' + data[i] + '">' + data[i] + '</option>'
                    );
                };
            },
            error:function () {
                alert('ERROR');
            },        });
    }
</script>