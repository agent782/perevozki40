<?php
    use yii\bootstrap\Html;
    use yii\widgets\Pjax;

$script = <<< JS
$(document).ready(function() {
    // setInterval(function(){ $("#refreshButton").click(); }, 1000);
});
JS;
$this->registerJs($script);
?>
<?php Pjax::begin([
    'id' => 'auto-find',
    'timeout' => 1000
]);?>

<div class="container">
    <br><br>
    <div class="h2"> <?= $time;?></div>
</div>
<?= Html::a("Обновить", ['/order/auto-find'],
    [
        'hidden' => true,
        'id' => 'refreshButton',
    ])
?>

<?php Pjax::end()?>


