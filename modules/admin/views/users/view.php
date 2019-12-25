<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 18.01.2018
 * Time: 10:22
 */
/* @var \yii\web\View $this
 * @var \app\models\Profile $profile
 * @var \app\models\User $user
 * */

use yii\widgets\DetailView;
use yii\bootstrap\Html;
use yii\bootstrap\Tabs;
use yii\helpers\Url;
use app\models\User;

$this->title = Html::encode('Информация о профиле');
?>
<div class="container">
    <?= $this->title?>
<br>


<h4><?= $profile->fioFull . ' (' . $profile->getRolesToString() . ')' ?></h4>
<?= Tabs::widget([
    'items' => [
        [
            'label' => 'Инфо',
            'content' => $this->render('profile-info', ['profile' => $profile])
        ],
        [
            'label' => 'Баланс',
            'content' => $this->render('@app/views/user/balance', User::arrayBalanceParamsForRender($model->id))
        ],
        [
            'label' => 'Транспорт',
            'content' => $this->render('@app/views/vehicle/index', [
                'searchModel' => $searchModelVehicle,
                'dataProviderTruck' => $dataProviderTruck,
                'dataProviderPass' => $dataProviderPass,
                'dataProviderSpec' => $dataProviderSpec,
                'dataProviderDeleted' => $dataProviderDeleted
            ])
        ],

    ]
])?>

</div>
