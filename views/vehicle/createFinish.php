<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 14.06.2018
 * Time: 8:59
 */
    use yii\bootstrap\ActiveForm;
    use yii\bootstrap\Html;
    use app\models\PriceZone;
    use app\components\widgets\ShowMessageWidget;
    use yii\widgets\MaskedInput;
    use app\models\Vehicle;
    use app\models\Tip;
$this->registerJsFile('/js/signup.js');
$this->title = Html::encode('Регистрационные данные транспортного средства.');
//$Vehicles = Vehicle::find()
//    ->where(['id_user' => Yii::$app->user->id])
//    ->all()
//;
//$reg_numbers = [];
//foreach ($Vehicles as $vehicle){
//    $reg_numbers[] = $vehicle->regLicense->reg_number;
//}
?>
<div class="container-fluid">
<?php
    $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
//        'enableClientValidation' => true,
        'validationUrl' => \yii\helpers\Url::to('validate-vehicle-form') // без этого не работает валидация  уникальности гос номера
    ]);
echo $form->field($modelRegLicense, 'id_user')->hiddenInput()->label(false);

$classiferVehicleIds = [];

?>
<h4><?= $this->title?></h4>
<br>
<div class="row">

    <div class="col-lg-4">
        <?=
        $form->field($VehicleForm, 'price_zones[]')->checkboxList($VehicleForm->getPriceZones(), [
        'item' => function ($index, $label, $name, $checked, $value) use ($id_user){
            $PriceZone = PriceZone::find()
                ->where(['id' => $value, 'status' => PriceZone::STATUS_ACTIVE])
                ->one()
                ->getPriceZoneForCarOwner($id_user);
        $return = '<label>';
            $return .= '<input type="checkbox" name="' . $name . '"' . 'value="' . $value . '"' . ' >' . "\n";
            $return .= '<i class="fa fa-square-o fa-2x"></i>' . "\n" .
            '<i class="fa fa-check-square-o fa-2x"></i>' . "\n";
            $return .= '<span>' . ucwords($label) . '</span>' . "\n";
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
        }
        ])
        ->label('Доступные тарифные зоны для вашего ТС:'
            . Tip::getTipButtonModal($VehicleForm, 'price_zones'))
            ?>
        <br>
        <?= $form->field($VehicleForm, 'photo')->fileInput([
            'multiple'=>false,
            'id' =>'pathPhoto'
        ]) ?>
        <br>
        <?= Html::img('/img/noPhoto.jpg', ['id' => 'photoPreview', 'class' => 'profile_photo_min'])?>
    </div>
<div class="col-lg-4">
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
    <?= $form->field($modelRegLicense, 'image1')->fileInput([
        'id' => 'pathPhoto1'
    ])?>
    <?= Html::img('/img/noPhoto.jpg', ['id' => 'photoPreview1', 'class' => 'profile_photo_min'])?>
<br><br>
    <?= $form->field($modelRegLicense, 'image2')->fileInput([
        'id' => 'pathPhoto2'
    ])?>
    <?= Html::img('/img/noPhoto.jpg', ['id' => 'photoPreview2', 'class' => 'profile_photo_min'])?>

    </div>

</div>
<div class="col-lg-12">
    <?= $form->field($VehicleForm, 'instruction_to_driver')->checkbox()
        ->label('Я ознакомлен и согласен с '
        . Html::a('"Памяткой водителю".', '/default/driver-instruction'))?>
    <?= $form->field($VehicleForm, 'confidentiality_agreement')
        ->checkbox()->label('Я ознакомлен и согласен с '
        . Html::a('"Соглашением о конфиденциальности".', '/default/policy'))?>
    <?= $form->field($VehicleForm, 'use_conditions')->checkbox()
        ->label('Я ознакомлен и согласен с '
        . Html::a('"Условиями использования сервиса perevozki40.ru".', '/default/user-agreement'))?>

</div>
    <div class="col-lg-12">
        <?= Html::submitButton('Назад', ['class' => 'btn btn-warning', 'name' => 'button', 'value' => 'create_back3']) ?>
        <?= Html::submitButton('Далее', ['class' => 'btn btn-success', 'name' => 'button', 'value' => 'create_finish']) ?>
    </div>
<?php
    ActiveForm::end();
?>
</div>
