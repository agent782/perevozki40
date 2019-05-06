<?php
    use app\components\widgets\AddCompany;
?>
<label>Юр. лица</label>
<?= Html::a(Html::icon('plus'), ['id' => 'addCompanyButton','class' => 'btn btn-primary']);?>


<?=
//var_dump($companies);
    \yii\bootstrap\Html::activeRadioList($modelOrder, 'id_company', $companies);
?>


