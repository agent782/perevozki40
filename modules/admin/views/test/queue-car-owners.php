<?php
/* @var $this yii\web\View */
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
$this->title = 'Тест поиска ТС по заказу.';
?>

<h4><?= $this->title?></h4>

<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'id_order')?>
<?= Html::submitButton()?>
<?php $form::end();?>
<?= \kartik\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'user.rolesString',
        'user.status',
        [
            'value' => function(\app\models\Vehicle $model) use ($order){
                return $model->getMinRate($order)->r_km;
            }
        ],
        'profile.balanceCarOwnerPayNow',
        'profile.balanceCarOwnerSum',

    ],
    'profile.fioShort'
])?>
