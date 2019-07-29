<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\settings\SettingSMS */

$this->title = 'Настройка СМС уведомлений';
\yii\web\YiiAsset::register($this);
?>
<div class="setting-sms-view">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'sms_code_update_phone',
        ],
    ]) ?>

</div>
