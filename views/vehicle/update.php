<?php

use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\BodyType;
use app\models\LoadingType;
use app\components\widgets\ShowMessageWidget;
use app\models\Vehicle;
use app\models\PriceZone;
use yii\widgets\MaskedInput;


/* @var $this yii\web\View */
/* @var $model app\models\Vehicle */
$this->registerJsFile('/js/signup.js');
$this->registerJsFile('/js/jsibox_basic.js');
$this->title = 'Редактирование данных: ' . $model->brand . ' ' . $model->regLicense->reg_number;
//$this->params['breadcrumbs'][] = ['label' => 'Vehicles', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
//$this->params['breadcrumbs'][] = 'Update';

$vehTypeId = $model->id_vehicle_type;

$BodyTypes = ArrayHelper::map(BodyType::find()->where(['id_type_vehicle' => $vehTypeId])->asArray()->all(), 'id', 'body');
unset($BodyTypes[0]);
$LoadingTypes = ArrayHelper::map(LoadingType::find()->asArray()->all(), 'id', 'type');
unset($LoadingTypes[0]);
$imgBTs = ArrayHelper::map(BodyType::find()->asArray()->all(), 'id', 'image');
$descBTs = ArrayHelper::map(BodyType::find()->asArray()->all(), 'id', 'description');
$imgLTs = ArrayHelper::map(LoadingType::find()->asArray()->all(), 'id', 'image');
$descLTs = ArrayHelper::map(LoadingType::find()->asArray()->all(), 'id', 'description');

$classiferVehicleIds = [];
$this->registerJsFile('/js/update_price_zones.js');

?>
<!--</script>-->

<div class="container-fluid">
    <h2><?= Html::encode($this->title) ?></h2>
    <?php
        $form = \yii\bootstrap\ActiveForm::begin([
            'enableAjaxValidation' => true,
            'validationUrl' => [Url::to('/vehicle/validate-vehicle-form')]
        ]);
//     Нужен для валидации на уникальность рег номера
    echo $form->field($modelRegLicense, 'id_user')->hiddenInput()->label(false);

    ?>

    <input id="id_vehicle" value="<?=$model->id?>" hidden></input>
    <input id="vehtype" value="<?=$model->id_vehicle_type?>" hidden></input>
    <div class="row">

        <div class="col-lg-4">
            <?php
            $thisPriceZones = $model->getPriceZones($model, $model->id_vehicle_type);
//            var_dump($thisPriceZones);
            echo $form->field($model, 'Price_zones[]')->checkboxList($thisPriceZones, [
                'item' => function ($index, $label, $name, $checked, $value)use($model){
                    $PriceZone = PriceZone::find()
                        ->where(['id' => $value])
                        ->andWhere(['status' => PriceZone::STATUS_ACTIVE])
                        ->one()
                        ->getPriceZoneForCarOwner($model->id_user);
                    $return = '<label>';
                    $return .= '<input type="checkbox" name="' . $name . '"';
                    foreach ($model->price_zones as $price_zone){
                        if($price_zone->id === $value) $return .= ' checked';
                    }
                    $return .= ' value="' . $value . '"' . ' >' . "\n";
                    $return .= '<i class="fa fa-square-o fa-2x"></i>' . "\n" .
                        '<i class="fa fa-check-square-o fa-2x"></i>' . "\n";
                    $return .= '<span style="font-size: x-small">' . ($label) . '</span>' . "\n";
                    $return .= '</label>'
                        . '<p style="font-size: x-small; font-style: italic">'
                        . $PriceZone->r_km . ' р/км '
                        . ', '
                        . $PriceZone->r_h . ' р/час'
                        . ' ...'
                        .  ShowMessageWidget::widget([
                            'helpMessage' => $PriceZone->printHtml(),
                            'ToggleButton' => ['label' => '<img src="/img/icons/help-25.png">', 'class' => 'btn'],
                        ])
                        . '</p>'
                    ;
                    return $return;
                },
                'id' => 'PriceZones',
//                'onchange' => 'alert();'
            ])
                ->label('Доступные тарифные зоны для вашего ТС:')
            ?>
            <br>

        </div>

    <div class="col-lg-4">
    <?php

    switch ($vehTypeId){
        case 1:
            echo $this->render('update_truck',
                ['model' => $model,  'form' => $form]);
            break;
        case 2:
            echo $this->render('update_pass',
            ['model' => $model, 'form' => $form]);
            break;
        case 3:
            echo $this->render('update_spec',
                ['model' => $model,  'form' => $form]);
            break;
        default:
            break;
    }

    ?>
        </div>
        <br>
    <div class="col-lg-4">
        <?php
//        if($model->id_vehicle_type != Vehicle::TYPE_SPEC) {
            echo $form->field($model, 'body_type')->radioList($BodyTypes, [
                    'item' => function ($index, $label, $name, $checked, $value) use ($imgBTs, $descBTs, $model) {
                        $return = '<label>';
                        $return .= '<input type="radio"';
                        $return .= ($model->id_vehicle_type != Vehicle::TYPE_SPEC)?'':' disabled="disabled" ';
                        if ($value === $model->body_type) $return .= ' checked ';
                        $return .= ' name="' . $name . '"' . 'value="' . $value . '"' . ' >' . "\n";
                        $return .= '<i class="fa fa-square-o fa-2x"></i>' . "\n" .
                            '<i class="fa fa-check-square-o fa-2x"></i>' . "\n";
                        $return .= '<span>' . ucwords($label) . '</span>' . "\n";
                        $return .= '</label>';
                        $return .= ' ' . ShowMessageWidget::widget([
                                'helpMessage' => '<img src= /img/imgBodyTypies/' . $imgBTs[$value] . '> </img> <br> <br>' . $descBTs[$value],
                                'ToggleButton' => ['label' => '<img src="/img/icons/help-25.png">', 'class' => 'btn'],
                            ]);
                        $return .= '<br>';
                        return $return;
                    },
                    'id' => 'body_type',
                    'onchange' => '
                    UpdatePriceZones();
                    var body_type = $(this).find("input:checked").val();
                    $("#sizes, #div_tonnage, #sizes_spec, #div_volume_spec, #div_volume").find("input").val("");

                    if(body_type == 8){
                        $("#sizes, #div_tonnage, #sizes_spec").show();
                        $("#div_volume_spec, #div_volume").find("input").val("0");
                        $("#div_volume_spec, #div_volume").hide();
                     }
                     if(body_type == 12){
                        $("#sizes_spec").show();
//                        $("#sizes, #div_tonnage, #div_volume_spec, #div_volume").find("input").val("0");
                        $("#sizes, #div_tonnage, #div_volume_spec, #div_volume").hide();

                     }
                     if(body_type == 13 || body_type == 15){
                        $("#div_volume_spec").show();
//                        $("#sizes, #div_tonnage, #sizes_spec, #div_volume").find("input").val("0");
                        $("#sizes, #div_tonnage, #sizes_spec, #div_volume").hide();

                     }
                     if(body_type == 14){
                        $("#div_volume, #div_tonnage").show();
//                        $("#sizes, #sizes_spec, #div_volume_spec").find("input").val(null);
                        $("#sizes, #sizes_spec, #div_volume_spec").hide();

                     }     
                '
                ]) . '<br>';
//        }
        if ($vehTypeId === \app\models\Vehicle::TYPE_TRUCK) {
            echo $form->field($model, 'loadingTypeIds[]')->checkboxList($LoadingTypes, [
                    'item' => function ($index, $label, $name, $checked, $value) use ($imgLTs, $descLTs, $model) {


                        $return = '<label>';
                        $return .= '<input type="checkbox" name="' . $name . '"';
                        foreach ($model->loadingtypes as $loadingtype) {
                            if ($value === $loadingtype->id) $return .= ' checked';
                        }
                        $return .= ' value="' . $value . '"' . ' >' . "\n";
                        $return .= '<i class="fa fa-square-o fa-2x"></i>' . "\n" .
                            '<i class="fa fa-check-square-o fa-2x"></i>' . "\n";
                        $return .= '<span>' . ucwords($label) . '</span>' . "\n";
                        $return .= '</label>';
                        $return .= ' ' . ShowMessageWidget::widget([
                                'helpMessage' => '<img src= /img/imgLoadingTypies/' . $imgLTs[$value] . '> </img> <br> <br>' . $descLTs[$value],
                                'ToggleButton' => ['label' => '<img src="/img/icons/help-25.png">', 'class' => 'btn'],
                            ]);
                        $return .= '<br>';

                        return $return;
                    }
                ]) . '<br>';
        }
        ?>

        <?= $form->field($model, 'description')->textarea()?>

    </div>
</div>
<br>
    <div class="row">

        <div class="col-lg-4">
<!--            Нужен для валидации на уникальность рег номера-->
            <?= $form->field($modelRegLicense, 'id')->hiddenInput()?>

            <?= $form->field($modelRegLicense, 'country')->dropDownList(\yii\helpers\ArrayHelper::map(
                (($q = new \yii\db\Query())
                    ->select(['id_country', 'name'])
                    ->from('country')
                    ->all()
                ), 'id_country', 'name'
            ), [
                'id' => 'countryDwnList',
                'class' => 'btn btn-primary',
                'onchange' => '
            $("#licenseMask").val("");
            $("#numberMask").val("");
            if($(this).val() == 1){
                $("#licenseMask").inputmask("99 99 № 999999");
                $("#licenseMask").attr("type", "tel");
                $("#numberMask").inputmask("* 999 ** 999");
            }
            else {
            $("#licenseMask").inputmask("");
                $("#licenseMask").attr("type", "text");
                $("#licenseMask").inputmask("");
                $("#numberMask").inputmask("");
            }
        '

            ] ) ?>
            <?= $form->field($modelRegLicense, 'brand')->textInput()?>
            <?= $form->field($modelRegLicense, 'reg_number')
                ->widget(MaskedInput::className(),[
                    'mask' => '* 999 ** 999',
                    'options' =>
                        [
                            'id' => 'numberMask',
                            'placeholder' => 'А999АА 999',
                            'type' => 'text',
                            'autocorrect' => 'off',
                            'autocomplete' => 'on'
                        ],
                    'clientOptions'=>[
                        'removeMaskOnSubmit' => true,

                    ],
                ])?>
            <?= $form->field($modelRegLicense, 'number')
                ->widget(MaskedInput::className(),[
                    'mask' => '99 99 № 999999',
                    'options' =>
                        [
                            'id' => 'licenseMask',
                            'placeholder' => '99 99 № 999999',
                            'type' => 'tel',
                            'autocorrect' => 'off',
                            'autocomplete' => 'on'
                        ],
                    'clientOptions'=>[
                        'removeMaskOnSubmit' => true,
                    ],
                ])?>
            <?= $form->field($modelRegLicense, 'date')
                ->widget(MaskedInput::className(),[
                    'clientOptions' => [
                    ],
                    'mask' => '99.99.9999',
                    'options' => [
                        'type' => 'tel',
                        'autocorrect' => 'off',
                        'autocomplete' => 'date',
                        'placeholder' => '01.01.2000'
                    ]
                ])?>
            <?= $form->field($modelRegLicense, 'place')
                ->textarea([
                    'placeholder' => 'Кем выдано...'
                ])?>
        </div>

        <div class="col-lg-4">
            <?= $form->field($modelRegLicense, 'image1File')->fileInput([
                'id' => 'pathPhoto1'
            ])?>

            <?=($modelRegLicense->image1)?
                ShowMessageWidget::widget([
                    'helpMessage' => Html::img(
                        '/uploads/photos/reg_licenses/'.$modelRegLicense->image1, ['style' => 'width: 90%; height: auto;' ]),
                    'ToggleButton' => ['label' => Html::img(
                        '/uploads/photos/reg_licenses/'.$modelRegLicense->image1, ['id' => 'photoPreview1', 'class' => 'profile_photo_min'])
                    ],
                ]):
                Html::img('/img/noPhoto.jpg', ['id' => 'photoPreview1', 'class' => 'profile_photo_min'])
            ?>

            <br><br>
            <?= $form->field($modelRegLicense, 'image2File')->fileInput([
                'id' => 'pathPhoto2'
            ])?>

            <?=($modelRegLicense->image2)?
                ShowMessageWidget::widget([
                    'helpMessage' => Html::img(
                        '/uploads/photos/reg_licenses/'.$modelRegLicense->image2, ['style' => 'width: 90%; height: auto;' ]),
                    'ToggleButton' => ['label' => Html::img(
                        '/uploads/photos/reg_licenses/'.$modelRegLicense->image2, ['id' => 'photoPreview2', 'class' => 'profile_photo_min'])
                    ],
                ]):
                Html::img('/img/noPhoto.jpg', ['id' => 'photoPreview2', 'class' => 'profile_photo_min'])
            ?>
        </div>
        <div class="col-lg-4">
        <?= $form->field($model, 'photoFile')->fileInput([
            'multiple'=>false,
            'id' =>'pathPhoto'
        ]) ?>
        <br>
        <?=
        ($model->photo)?
            ShowMessageWidget::widget([
                'helpMessage' => Html::img(
                    '/uploads/photos/vehicles/'.$model->photo, ['style' => 'width: 90%; height: auto;' ]),
                'ToggleButton' => ['label' => Html::img(
                    '/uploads/photos/vehicles/'.$model->photo, ['id' => 'photoPreview', 'class' => 'profile_photo_min'])
                ],
            ]):
            Html::img('/img/noPhoto.jpg', ['id' => 'photoPreview', 'class' => 'profile_photo_min']);
        ?>
        </div>
    </div>
<br>

    <div class="row">
        <div class="col-lg-12">
            <?=Html::a('Отмена', Url::to(Yii::$app->user->can('admin')? ' /admin/vehicle':'/vehicle'), ['class' => 'btn btn-warning']) ?>

            <?=Html::submitButton(($model->status != Vehicle::STATUS_DELETED)?'Сохранить':'Восстановить и сохранить', ['class' => 'btn btn-success'])?>
        </div>
    </div>
</div>
<?php
//var_dump($model->body_type);
    \yii\bootstrap\ActiveForm::end();
?>

</div>
