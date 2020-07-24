<?php
    use yii\bootstrap\Html;
    use yii\widgets\Pjax;

$script = <<< JS
$(document).ready(function() {
    $.pjax.reload({
       container: '#refresh-time',
       timeout: 1000,
       // url: '/order/refresh-time',
       // dataType:"json",
       // type: "POST"
    });
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
