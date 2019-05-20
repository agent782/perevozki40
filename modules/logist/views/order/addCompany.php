<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 08.04.2019
 * Time: 14:49
 */
use yii\bootstrap\Html;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
$this->title = 'Выбор юр. лица';
?>
<div>
    <h3><?=$this->title?></h3>


        <?= Html::a(Html::icon('plus'),
            ['/company/create', 'user_id' => $modelOrder->id_user,
                'redirect' => \yii\helpers\Url::to(['/logist/order/add-company', 'id_order' => $modelOrder->id])],
            ['class' => 'btn btn-primary']);?>

        <label>Юр. лица клиента</label>

    </div>
    <div class="col-lg-4">
        <?php
//        Pjax::begin([
//            'id' => 'create-company'
//        ]);
            $form = ActiveForm::begin([

            ]);
            echo $form->field($modelOrder, 'id_company')->radioList($companies, [
                'id' => 'id_company',
                'onchange' => new \yii\web\JsExpression('
//                    alert($(this).find(":checked").val());
                    $.pjax.reload({
                        url : "/logist/order/pjax-company-info",
                        container: "#info",
//                        dataType:"json",
                        type: "POST", 
                        data: {  
                              "id_company" : $(this).find(":checked").val() 
                         }
                    })
                ')
            ])->label('Выберите юр. лицо');
            echo Html::submitButton();

            $form::end();
//        Pjax::end();
        ?>
    </div>
<div class="col-lg-4">
    <label>Информация о юр. лице</label>
    <?php
        Pjax::begin(['id' => 'info']);
    ?>

    <?php
        Pjax::end();
    ?>
</div>

</div>