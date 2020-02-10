<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 11.12.2019
 * Time: 9:41
 */
use kartik\grid\GridView;
use app\models\Profile;
use yii\bootstrap\Html;
use yii\helpers\Url;

?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'responsiveWrap' => false,
    'filterModel' => $modelSearch,
    'columns' => [
        [
            'attribute' => 'id',
            'format' => 'raw',
            'value' => function (Profile $model){
                return Html::a($model->old_id, Url::to(['/admin/users/view', 'id' => $model->id_user]));
            }
        ],
        'old_id',
        'fioFull',
        [
            'label' => 'Баланс к выплате',
            'attribute' => 'balanceCarOwnerPayNow',
            'filter' => false

        ],
        [
            'label' => 'Баланс к выплате после проплат клиентами',
            'attribute' => 'balanceCarOwnerSum',
            'filter' => false

        ],

    ]
]);
?>
