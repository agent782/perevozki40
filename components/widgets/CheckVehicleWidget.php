<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 20.04.2018
 * Time: 11:11
 */

namespace app\components\widgets;
use app\components\DateBehaviors;
use app\models\Vehicle;
use yii\bootstrap\Modal;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\bootstrap\Widget;
use yii\helpers\Url;
use yii\widgets\DetailView;


class CheckVehicleWidget extends Widget{
    public $modelVehicle;

    public function run()
    {
        self::checkVehicle(); // TODO: Change the autogenerated stub
    }

    public function checkVehicle(){
        $model = $this->modelVehicle;
        $veh_type = $model->id_vehicle_type;
        $template = '<tr><th>{label}</th><td style="width:90%;">{value}</td></tr>';


        $DetailViewTruck = DetailView::widget([
            'model' => $this->modelVehicle,
            'attributes' => [
                'id',
                'create_at',
                'update_at',
                'bodyTypeText',
                'tonnage',
                'length',
                'width',
                'height',
                'volume',
                'loadingtypesText',
                'passengers',
                [
                    'attribute' => 'longlengthIcon',
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'priceZonesList',
                    'format' => 'raw'
                ],
            ],

            'template' => $template
        ]);
        $DetailViewPass = DetailView::widget([
            'model' => $this->modelVehicle,
            'attributes' => [
                'id',
                'create_at',
                'update_at',
                'passengers',
                'bodyTypeText',
                'tonnage',
                'volume',
                [
                    'attribute' => 'priceZonesList',
                    'format' => 'raw'
                ],
            ],
            'template' => $template
        ]);
            $DetailViewSpec = DetailView::widget([
                'model' => $this->modelVehicle,
                'attributes' => [
                    'id',
                    'create_at',
                    'update_at',
                    'bodyTypeText',
                    'tonnage',
                    'length',
                    'width',
                    'height',
                    'volume',
                    'tonnage_spec',
                    'length_spec',
                    'volume_spec',
                    [
                        'attribute' => 'priceZonesList',
                        'format' => 'raw'
                    ],

                ]
            ]);
        Modal::begin([
            'header' => 'Проверка нового ТС',
            'toggleButton' => ['label' => 'Проверить.', 'class' => 'btn btn-success btn-xs'],
            'options' => ['style' => 'width: 100%;']
        ]);
?>
        <div class="row" style="font-size: small; line-height: 100%;">

            <div class="col-lg-4">
                <h4>Характеристики.</h4>
                <?php switch ($veh_type){
                    case Vehicle::TYPE_TRUCK:
                        echo $DetailViewTruck;
                        break;
                    case Vehicle::TYPE_PASSENGER:
                        echo $DetailViewPass;
                        break;
                    case Vehicle::TYPE_SPEC:
                        echo $DetailViewSpec;
                        break;
                }
                ?>
            </div>
        <div class="col-lg-4">
            <h4>Регистрационные данные.</h4>
            <?= DetailView::widget([
                'model' => $this->modelVehicle,
                'attributes' => [
                    'regLicense.id',
                    'regLicense.brand.brand',
                    'regLicense.reg_number',
                    'regLicense.number',
                    'regLicense.date',
                    'regLicense.place'
                ],
                'template' => $template
            ]);
            ?>
        </div>
            <div class="col-lg-4">
                <h4>Автовладелец.</h4>
                <?= DetailView::widget([
                    'model' => $this->modelVehicle,
                    'attributes' => [
                        'id_user',
                        'profile.fioFull',
                        'profile.user.username',
                        'profile.create_at',
                        'profile.passport.number',
                        'profile.reg_address'

                    ],
                    'template' => $template
                ]);
                ?>
            </div>

        </div>
        <div class="row">
            <?=
                DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        [
                            'attribute' => 'photoHtml',
                            'format' => 'raw'
                        ],
                        [
                            'attribute' => 'regLicense.image1Html',
                            'format' => 'raw'
                        ],
                        [
                            'attribute' => 'regLicense.image2Html',
                            'format' => 'raw'
                        ],

                    ]
                ])
            ?>
        </div>
<div class="container-fluid row">
<?php
        $form = ActiveForm::begin([
            'action' => Url::to(['check', 'id' => $model->id, 'id_user' => $model->id_user]),
            'method' => 'POST',
            'validateOnSubmit' => false
        ]);
        echo $form->field($model, 'error_mes')->textarea()->label('Причина отказа.');
        echo Html::submitButton('Активировать', [
                'class' => 'btn btn-success',
            'name' => 'button',
            'value' => 'success'
            ]) . ' ';
        echo Html::submitButton('Отказать', [
            'class' => 'btn btn-warning',
            'name' => 'button',
            'value' => 'error'
        ]);


        $form::end();

        Modal::end();
    }
}
?>
</div>
