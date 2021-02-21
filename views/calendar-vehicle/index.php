<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CalendarVehicleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Календарь занятости';
?>
<div class="calendar-vehicle-index">

    <h4><?= Html::encode($this->title) ?></h4>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
        foreach ($Vehicles as $key => $vehicle){
            echo '<h4>'. $key .'</h4>' .
                GridView::widget([
                    'dataProvider' => $vehicle,
                    'columns' => [
                        'date',
                        'status'
                    ]
                ])
            ;
        }
    ?>

</div>
