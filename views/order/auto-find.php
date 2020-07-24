<?php
    use yii\bootstrap\Html;
    use yii\widgets\Pjax;

$script = <<< JS

$("document").ready(function(){
        setTimeout(function(){
            $.pjax.reload({container:"#refresh-time"});  //Reload GridView
        },1000);
});
JS;
$this->registerJs($script);
?>
<?php Pjax::begin([
    'id' => 'refresh-time',
//    'timeout' => 1
]);?>

<div class="container">
    <br>
    <div class="h2"> <?= $time;?></div>
</div>


<?=
    \kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
        ]
    ])
?>

<?php Pjax::end()?>
