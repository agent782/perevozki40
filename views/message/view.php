<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\rating\StarRating;


/* @var $this yii\web\View */
/* @var $model app\models\Message */

\yii\web\YiiAsset::register($this);
 ?>
<div class="message-view">
    <h4><?= $modelMessage->title?></h4>
    <br>
    <?= $modelMessage->text?>
    <?php
        if($modelMessage->can_review_client || $modelMessage->can_review_vehicle) {
            $form = ActiveForm::begin([

            ]);
            echo $form->field($modelReview, 'value')->widget(StarRating::class, [

                'pluginOptions' => [
                    'step' => 1,
                    'language' => 'ru',
                    'filledStar' => Html::img('/img/icons/truck-filled.png'),
                    'emptyStar' => Html::img('/img/icons/truck-empty.png'),
                    'starCaptions' => [
                        1 => 'Очень плохо',
                        2 => 'Плохо',
                        3 => 'Нормально',
                        4 => 'Хорошо',
                        5 => 'Очень хорошо',
                    ],
                ]
            ])->label(($modelMessage->can_review_client)
                ? 'Оцените действие водителя.'
                : 'Оцените действие заказчика.');
            echo $form->field($modelReview, 'comment')->textarea();

            ActiveForm::end();
        }
?>
    <br><br>
    <?= $modelMessage->create_at?>
</div>
