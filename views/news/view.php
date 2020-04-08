<?php

use yii\bootstrap\Html;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\News */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'News', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="news-view">

    <h3><?= Html::encode($this->title) ?></h3>
    <br>
    <?=
        Html::a(
            Html::icon('plus') . ' ' .$model->rating_up,
            '#',
            [
                'class' => 'btn btn-xs btn-success',
                'onclick' => new JsExpression('
                    alert();    
                ')
            ]
        )

    ?>
    <?=
    Html::a(
        Html::icon('minus') . ' ' .$model->rating_down,
        '#',
        [
            'class' => 'btn btn-xs btn-danger',
            'onclick' => new JsExpression('
                alert();    
            ')
        ]
    )
    ?>

    <br>
    <p>
        <?=$model->text?>
    </p>
    <br>
    <p>
        <?= $model->create_at?>
    </p>
    <p>
        <?php if(Yii::$app->user->can('admin')) {
            echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-xs btn-primary']);
            echo ' ';
            echo Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-xs btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]);
        }
        ?>
    </p>


</div>
