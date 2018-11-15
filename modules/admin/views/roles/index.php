<?php
    use yii\grid\GridView;
    use yii\bootstrap\ActiveForm;
    use yii\bootstrap\Tabs;
    use yii\bootstrap\Html;
    $this->title = Html::encode('RBAC Роли');
?>

<?= $this->title?>
    <hr>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
            'name',
            'created_at:date',
            'updated_at:date',
        ['class' => 'yii\grid\ActionColumn'],
    ],
]); ?>
<br>
<?php $form = ActiveForm::begin()?>
<?= $form->field( $model_auth_item, 'name') ?>
<?= Html::submitButton('Добавить',['class' => 'btn btn-success']) ?>
<?php ActiveForm::end()?>
<br>
<?php
if(Yii::$app->session->hasFlash('access')){
    echo Yii::$app->session->getFlash('access');
} elseif(Yii::$app->session->hasFlash('error')){
    echo Yii::$app->session->getFlash('error');
}
?>
