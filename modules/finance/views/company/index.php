<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\models\Company;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CompanySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Организации и ИП';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Company', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
//        'pjax' => true,
        'responsiveWrap' => false,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'inn',
                'filter' => true
            ],
            [
                'attribute' => 'name',
                'filter' => \yii\jui\AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => 'name',
                    'clientOptions' => [
                        'source' => Company::find()->select(['name_full as value', 'name_full as label'])->asArray()->all(),
                        'autoFill' => true,
                    ]
                ])
            ],
            [
              'attribute' => 'inn',
                'filter' => false
            ],
            [
                'attribute' => 'balanceSum',
                'label' => 'Баланс',
                'format' => 'raw',
                'filter' => false
//                'value' => function (Company $company){
//                    return $company->balance['balance'];
//                }
            ],


//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
