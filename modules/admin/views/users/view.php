<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 18.01.2018
 * Time: 10:22
 */
use yii\widgets\DetailView;
use yii\bootstrap\Html;
$this->title = Html::encode('Информация о профиле');
?>
<br>
<?php
foreach ($model->roles as $role) echo $role->name . ' ';
echo DetailView::widget([
        'model' => $profile,
    ]);
   echo DetailView::widget([
        'model' => $model,
    ]);
?>