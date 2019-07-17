<?php
/* @var $modelDriverLicense \app\models\DriverLicense*/
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 15.05.2018
 * Time: 14:39
 */
use yii\widgets\MaskedInput;
use kartik\datetime\DateTimePicker;
//    use yii\jui\DatePicker;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use app\models\signUpClient\SignUpClientFormStart;
use yii\helpers\Json;
use yii\helpers\FormatConverter;

$this->title = Html::encode('Регистрация автовладельца');
$this->registerJsFile('/js/signup.js');
?>

<h2><?=$this->title?></h2>
<p>Заполните все поля формы.</p>
<div class="row">

    <?php
        $form = ActiveForm::begin([
            'enableAjaxValidation' => true,
            'validationUrl' => \yii\helpers\Url::to(['validate-passport']),
            'fieldConfig' => [
                'template' => "{label}<br>{input}<br>{error}",
            ],
        ]);
    ?>
    <div class="col-md-4 col-sm-4">
        <?= $form->field($modelStart, 'id_user')->hiddenInput()->label(false);?>
        <?= $form->field($modelStart, 'email') ->input('email')?>

        <?= $form->field($modelStart, 'phone2')
            ->widget(MaskedInput::className(),[
                'mask' => '+7(999)999-99-99',
                'clientOptions'=>[
                    'removeMaskOnSubmit' => true,
                ],
                'options' => [
                    'type' => 'tel',
                    'autocorrect' => 'off',
                    'autocomplete' => 'tel'
                ]
            ])?>

        <?= $form->field($modelStart, 'email2') ->input('email')?>

        <?=
        $form->field($modelStart, 'bithday')
            ->widget(MaskedInput::className(),[
                'clientOptions' => [
                ],
                'mask' => '99.99.9999',
                'options' => [
                    'type' => 'tel',
                    'autocorrect' => 'off',
                    'autocomplete' => 'date',
                    'placeholder' => '31.01.1980'
                ]
            ])
        ;?>

        <?= $form->field($modelStart, 'is_driver')->radioList(['Нет', 'Да'],
            [
                'id' => 'is_driver',
                'onchange' => '
                    if($(this).find("input:checked").val() == 0){
                        $("#number").val("");
                        $("#driver_license").attr("hidden", true);
                    } else {
                        $("#driver_license").attr("hidden", false   );
                    }
                '
        ])->label('Вы будете выполнять заказы в качестве водителя?')?>

        <div id="driver_license" hidden>
            <?= $form->field($modelStart, 'driver_licence_number')->input('text', ['id' => 'number']);?>
            <?= $form->field($modelStart, 'driver_licence_date') ->widget(MaskedInput::className(),[
                'clientOptions' => [
                ],
                'mask' => '99.99.9999',
                'options' => [
                    'type' => 'tel',
                    'autocorrect' => 'off',
                    'autocomplete' => 'date',
                    'placeholder' => '31.01.1980'
                ]
            ]);?>
            <?= $form->field($modelStart, 'driver_licence_place')->textarea();?>
        </div>
    </div>

    <div class="col-md-4 col-sm-4">
        <?= $form->field($modelStart, 'country')->dropDownList(\yii\helpers\ArrayHelper::map(
            (($q = new \yii\db\Query())
                ->select(['id_country', 'name'])
                ->from('country')
                ->all()
            ), 'id_country', 'name'
        ), [
            'id' => 'countryPassportDwnList',
            'class' => 'btn btn-primary',

        ] )?>
        <?= $form->field($modelStart, 'passport_number')
            ->widget(MaskedInput::className(),[
                'mask' => '9999-999999',
                'options' =>
                    [
                        'id' => 'passportMask',
                        'placeholder' => 'Серия и номер',
                        'type' => 'tel',
                        'autocorrect' => 'off',
                        'autocomplete' => 'off'
                    ],
                'clientOptions'=>[
                    'removeMaskOnSubmit' => true,
                ],
            ])

        ?>
        <?= $form->field($modelStart, 'passport_date')
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
        <?= $form->field($modelStart, 'passport_place')->textarea(['placeholder'=>'Кем выдан']) ?>
        <?= $form->field($modelStart, 'reg_address')->textarea()?>
    </div>

    <div class="col-md-4 col-sm-4">

        <?= $form->field($modelStart, 'photo')->fileInput([
            'id' => 'pathPhoto'
        ]) ?>


        <?php
            if(!$modelProfile->photo) {
                echo Html::img('/img/noPhoto.jpg', ['id' => 'photoPreview', 'class' => 'profile_photo_min']);
            } else {
                echo Html::img('/uploads/photos/' . $modelProfile->photo, ['id' => 'photoPreview', 'class' => 'profile_photo_min']);
            }
        ?>
    </div>

    <div class="col-xs-12" style="margin: 10px">
        <?= $form->field($modelStart, 'assignAgreement')->checkbox()->label('Я прочитал и согласен с ' .
            Html::a('Соглашением об использовании сервиса perevozki40.ru', \yii\helpers\Url::to('/default/user-agreement')))?>
        <?= Html::submitButton('Далее',['class' => 'btn btn-success'])?>
        <?php
        ActiveForm::end();
        ?>
    </div>
</div>