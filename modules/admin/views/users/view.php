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
            'content' => $this->render('@app/views/user/balance',
                [
                    'dataProvider_car_owner' => $dataProvider_car_owner,
                    'dataProvider_user' => $dataProvider_user,
                    'dataProviders_companies' => $dataProviders_companies,
                    'balance' => $balance,
                    'Balance' => $Balance,
                    'ids_companies' => $ids_companies
                ])
//                User::arrayBalanceParamsForRender($model->id))
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
        [
            'label' => 'Календарь',
            'content' => $this->render('@app/views/vehicle/calendar',[
                'Vehicles' => $Vehicles,
                'ids_vehicles' => $ids_vehicles
            ]),
            'visible' => Yii::$app->user->can('admin')
                || Yii::$app->user->can('dispetcher'),

        ],
        [
            'label' => 'Администрирование',
            'visible' => Yii::$app->user->can('admin'),

        ],

    ]
])?>

</div>
