<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use kartik\rating\StarRating;



/* @var $this yii\web\View */
/* @var $model app\models\Message */

\yii\web\YiiAsset::register($this);
 ?>
<div class="message-view">
    <h4><?= $modelMessage->title ?></h4>
    <br>
    <?= $modelMessage->text?>
    <br>
    <?php
        if($modelMessage->can_review_client || $modelMessage->can_review_vehicle) {
            $form = ActiveForm::begin([

            ]);
            echo $form->field($modelReview, 'value')->widget(StarRating::class, [
                'pluginOptions' => [
                    'step' => 1,
                    'language' => 'ru',
                    'filledStar' => '<i class="glyphicon glyphicon-star" style="color: #821e82"></i>',
                    'emptyStar' => '<i class="glyphicon glyphicon-star"></i>',
                    'starCaptions' => [
                        1 => 'Очень плохо',
                        2 => 'Плохо',
                        3 => 'Нормально',
                        4 => 'Хорошо',
                        5 => 'Очень хорошо',
                    ],
                    'showClear' => false
                ]
            ])->label(($modelMessage->can_review_client)
                ? 'Оцените действие водителя.'
                : 'Оцените действие заказчика.');
            echo $form->field($modelReview, 'comment')->textarea();
            echo Html::a('Отмена', '/message', ['class' => 'btn btn-warning']) . ' ';
            echo Html::submitButton(
                    ($modelReview->isNewRecord)?'Оценить':'Изменить',
                            ['class' => 'btn btn-success']
            );
            ActiveForm::end();
        }
?>
    <br><br>
    <?= $modelMessage->create_at?>
</div>
