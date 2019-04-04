<label>Юр. лица</label>

<?=
//var_dump($companies);
    \yii\bootstrap\Html::activeRadioList($modelOrder, 'id_company', $companies);
?>


