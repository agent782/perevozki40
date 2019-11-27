<?php

use yii\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $searchModel app\models\PaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Платежи';
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
                'attribute' => 'id_user',
                'format' => 'raw',
                'value' => function($model){
                    $profile = $model->profile;
                    if($profile) {
                        return Html::a($profile->fioFull, Url::to(['/finance/profile/view', 'id' => $profile->id_user]));
                    }
                },
                'filter' => \yii\jui\AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => 'id_user',
                    'clientOptions' => [
                        'source' => \app\models\Profile::getArrayForAutoComplete(true),
                        'autoFill' => true,
                    ]
                ]),
                'headerOptions' => ['id' => 'header']
            ],
            [
                'label' => 'Юр. лицо',
//                'attribute' => 'companyName',
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
                        'autoFill' => true,
                    ]
                ])
            ],
            [
                'label' => 'Тип платежа',
                'attribute' =>'type_payments',
                'value' => function($model){
                    return $model->typePayment->min_text;
                },
                'filter' =>
                    Html::activeCheckboxList($searchModel, 'type_payments',
                        ArrayHelper::map(\app\models\TypePayment::find()->all(), 'id', 'min_text')
                    )
                ,
                'filterOptions' => ['class' => 'minRoute']
            ],
//
            ['class' => 'yii\grid\ActionColumn'],
        ]
    ]); ?>
</div>
