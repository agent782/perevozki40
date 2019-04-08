<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 08.04.2019
 * Time: 14:49
 */
use yii\bootstrap\Html;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
$this->title = 'Выбор юр. лица';
?>
<div>
    <h3><?=$this->title?></h3>
    <div class="col-lg-4">
        <label>Юр. лица клиента</label>
        <?= Html::a(Html::icon('plus'), ['/order/create', 'user_id' => Yii::$app->user->id], ['class' => 'btn btn-primary']);?>

        <?php
            Pjax::begin([

            ]);
        ?>
        <?= Html::activeRadioList($modelOrder, 'id_company', $companies)?>
        <?php
            Pjax::end();
        ?>

    </div>


</div>