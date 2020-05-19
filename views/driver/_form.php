<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model app\models\Driver */
/* @var $form yii\widgets\ActiveForm */
    $this->registerJsFile('/js/signup.js');

?>

<div class="driver-form">

    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
        'validationUrl' => \yii\helpers\Url::to('/driver/ajax-validation')
    ]); ?>
<div class="col-lg-4">
    <?php
        if($model->isNewRecord) {
            echo $form->field($DriverForm, 'surname')->textInput(['maxlength' => true]);
            echo $form->field($DriverForm, 'name')->textInput(['maxlength' => true]);
            echo $form->field($DriverForm, 'patronymic')->textInput(['maxlength' => true]);
        }
    ?>
    <?= $form->field($DriverForm, 'birthday')->textInput()
        ->widget(MaskedInput::className(),[
            'clientOptions' => [
            ],
            'mask' => '99.99.9999',
            'options' => [
                'type' => 'tel',
                'autocorrect' => 'off',
//                'autocomplete' => 'date',
                'placeholder' => '01.01.2000'
            ]
        ])
    ?>
    <?= $form->field($DriverForm, 'address')->textarea(['rows' => 4])?>
    <?= $form->field($DriverForm, 'phone')
        ->widget(MaskedInput::className(),[
            'mask' => '+7(999)999-99-99',
            'clientOptions'=>[
                'removeMaskOnSubmit' => true,
            ],
            'options' => [
                'type' => 'tel',
                'autocorrect' => 'off',
//                'autocomplete' => 'tel'
            ]
        ])
    ?>
    <?= $form->field($DriverForm, 'phone2')
        ->widget(MaskedInput::className(),[
            'mask' => '+7(999)999-99-99',
            'clientOptions'=>[
                'removeMaskOnSubmit' => true,
            ],
            'options' => [
                'type' => 'tel',
                'autocorrect' => 'off',
//                'autocomplete' => 'tel'
            ]
        ])
    ?>
</div>
    <div class="col-lg-4">
        <h4>Паспортные данные</h4>
        <?= $form->field($DriverForm, 'country')->dropDownList(\yii\helpers\ArrayHelper::map(
            (($q = new \yii\db\Query())
                ->select(['id_country', 'name'])
                ->from('country')
                ->all()
            ), 'id_country', 'name'
        ), [
            'id' => 'countryPassportDwnList',
            'class' => 'btn btn-primary',

        ] )?>
        <?= $form->field($DriverForm, 'number_passport')
            ->widget(MaskedInput::className(),[
                'mask' => '9999-999999',
                'options' =>
                    [
                        'id' => 'passportMask',
                        'placeholder' => 'Серия и номер',
//                        'type' => 'tel',
//                        'autocorrect' => 'off',
//                        'autocomplete' => 'off'
                    ],
                'clientOptions'=>[
                    'removeMaskOnSubmit' => true,
                ],
            ])

        ?>
        <?= $form->field($DriverForm, 'date_passport')
            ->widget(MaskedInput::className(),[
                'clientOptions' => [
                ],
                'mask' => '99.99.9999',
                'options' => [
                    'type' => 'tel',
//                    'autocorrect' => 'off',
//                    'autocomplete' => 'date',
                    'placeholder' => '01.01.2000'
                ]
            ])?>
        <?= $form->field($DriverForm, 'place_passport')->textarea(['placeholder'=>'Кем выдан'])
        ?>
        <br>
        <p><h4>Водительское удостоверение.</h4></p>
        <?= $form->field($DriverForm, 'number_license')?>

        <?= $form->field($DriverForm, 'date_license')
            ->widget(MaskedInput::className(),[
                'clientOptions' => [
                ],
                'mask' => '99.99.9999',
                'options' => [
                    'type' => 'tel',
//                    'autocorrect' => 'off',
//                    'autocomplete' => 'date',
                    'placeholder' => '01.01.2000'
                ]
            ])?>
        <?= $form->field($DriverForm, 'place_license')->textarea(['placeholder'=>'Кем выдан'])
        ?>
    </div>
    <div class="col-lg-4">
        <?= $form->field($DriverForm, 'photo')->fileInput([
            'id' => 'pathPhoto'
        ]) ?>
        <?= Html::img(($model->isNewRecord)
            ? '/img/noPhoto.jpg'
            : '/uploads/photos/drivers/'.$DriverForm->photo, ['id' => 'photoPreview', 'class' => 'profile_photo_min'])?>
    </div>

   <p><div class="col-lg-12 form-group">
        <?= Html::a('Отменить', \yii\helpers\Url::previous(), ['class' => 'btn btn-warning']) ?>
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    </p>

    <?php ActiveForm::end(); ?>
</div>

