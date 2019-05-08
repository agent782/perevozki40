<?php

use yii\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Расчеты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Html::icon('plus'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
//            'id',
            'date',
            [
                'label' => 'Дебет',
                'value' => function($model){
                    if($model->direction == \app\models\Payment::DEBIT){
                        return $model->cost;
                    }
                }
            ],
            [
                'label' => 'Кредит',
                'value' => function($model){
                    if($model->direction == \app\models\Payment::CREDIT){
                        return $model->cost;
                    }
                }
            ],
            [
                'label' => 'Пользователь',
                'format' => 'raw',
                'value' => function($model){
                    $profile = $model->profile;
                    return Html::a($profile->fioFull, Url::to(['/finance/profile/view', 'id' => $profile->id_user]));
                }
            ],
            [
                'label' => 'Юр. лицо',
                'attribute' => 'companyName',
                'format' => 'raw',
                'value' => function($model, $index, $value){
                    $company = $model->company;
                    if (!$company) return null;
                    return Html::a($company->name, Url::to(['/finance/company/view', 'id' => $company->id]));
                },
                'filter' => \yii\jui\AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => 'companyName',
                    'clientOptions' => [
                        'source' => \app\models\Company::getArrayForAutoComplete(true),
//                        'source' => \app\models\Company::find()->select(['name_full as value', 'name_full as label'])->asArray()->all(),
                        'autoFill' => true,
                    ]
                ])
            ],
            [
                'label' => 'Тип платежа',
                'attribute' =>'typePayment.min_text'
            ],
//            'id_payer_user',
            //'id_recipient_user',
            //'id_payer_company',
            //'id_recipient_company',
            //'status',
            'comments:ntext',
            //'sys_info:ntext',
            //'create_at',
            //'update_at',

//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
