<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 19.01.2018
 * Time: 9:33
 */
    use yii\widgets\MaskedInput;
    use kartik\datetime\DateTimePicker;
//    use yii\jui\DatePicker;
    use yii\bootstrap\ActiveForm;
    use yii\bootstrap\Html;
    use app\models\signUpClient\SignUpClientFormStart;
    use yii\helpers\Json;
    use yii\helpers\FormatConverter;

    $this->title = Html::encode('Регистрация клиента');
    $this->registerJsFile('/js/signup.js');
?>
<h3><?= $this->title?></h3>
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
        <?= $form->field($modelStart, 'surname');?>
        <?= $form->field($modelStart, 'name');?>
        <?= $form->field($modelStart, 'patrinimic');?>
        <?= $form->field($modelStart, 'email') ->input('email')?>
        <?= $form->field($modelStart, 'phone', ['inputOptions' => ['disabled' => true]]);?>
        <?= $form->field($modelStart, 'phone2')
            ->widget(MaskedInput::className(),[
                'mask' => '+7(999)999-99-99',
                'clientOptions'=>[
                    'removeMaskOnSubmit' => true,
                ],
                'options' => [
                    'type' => 'tel',
                    'autocorrect' => 'off',
//                    'autocomplete' => 'tel'
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
                    'placeholder' => '01.01.1980'
                ]
            ])
        ;?>
    </div>

    <div class="col-md-4 col-sm-4">
        <comment>Все личные данные клиентов хранятся в зашифрованном виде! Водители в первую очередь принимают заказы от Клиентов, с наиболее полной информацией в профиле (При аозможности
            заполняйте все поля!) и наивысшим рейтингом.</comment>
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
                    'type' => 'text',
//                    'autocorrect' => 'off',
//                    'autocomplete' => 'off'
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
        <?= $form->field($modelStart, 'passport_place')->textarea(['placeholder'=>'Кем выдан'])
        ?>
        <?= $form->field($modelStart, 'reg_address')?>
    </div>

    <div class="col-md-4 col-sm-4">

        <?= $form->field($modelStart, 'photo')->fileInput([
            'id' => 'pathPhoto'
        ]) ?>
        <?= Html::img('/img/noPhoto.jpg', ['id' => 'photoPreview', 'class' => 'profile_photo_min'])?>
    </div>

    <div class="col-xs-12" style="margin: 10px">
        <?= $form->field($modelStart, 'assignAgreement')->checkbox()->label('Я прочитал и согласен с ' .
            Html::a('Соглашением об использовании сервиса perevozki40.ru',
                \yii\helpers\Url::to(
                        '/default/user-agreement'
                )))?>
        <?= $form->field($modelStart, 'confidentiality_agreement')
            ->checkbox()->label('Я ознакомлен и согласен с '
                . Html::a('"Соглашением о конфиденциальности".', '/default/policy'))?>

        <?= Html::submitButton('Завершить',['class' => 'btn btn-success'])?>
    <?php
        ActiveForm::end();
    ?>
    </div>
</div>