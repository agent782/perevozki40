<?php

/* @var User $modelUser*/
/* @var Profile $modelProfile*/
/* @var UpdateUserProfileForm $UpdateUserProfileForm*/
/* @var \yii\web\View $this*/
    $this->registerJsFile('/js/updateUserProfileForm.js');
    $this->registerJsFile('/js/signup.js');
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 11.01.2018
 * Time: 17:35
 */
    use yii\bootstrap\Html;
    use app\models\User;
    use app\models\Profile;
    use app\models\UpdateUserProfileForm;
    USE yii\widgets\MaskedInput;

    $this->title = Html::encode(
             $modelProfile->fioFull) . ' (ID ' . $modelProfile->id_user . ')'
        . Html::a(' (Выйти)', '/default/logout', ['style' => 'font-size: 12px;']);
?>

    <div class="container">
        <label class="h3"><?= $this->title?></label>
        <br>
        <div class="row">
        <?php
            $form = \yii\bootstrap\ActiveForm::begin([
                'enableAjaxValidation' => true,
                'validationUrl' => '/user/validate-passport',
                'fieldConfig' => [
                    'inputOptions' => [
                        'style' => 'width:auto',
                        'readonly' => true
                    ]
                ]
            ]);
        ?>
            <div class="col-lg-12">
                <br><br>
                <?=Html::submitButton('Сохранить',
                    [
                        'class' => 'btn btn-success btn-block',
                        'disabled' => true
                    ])?>
            </div>
            <div class="col-lg-4" id="profile">
                <label class="h4">Профиль <?=
                        Html::a(Html::icon('edit', ['title' => 'Редактировать']), '#', ['id' => 'edit_profile']);
                    ?>
                </label>

                <br><br>

                <?= $form->field($UpdateUserProfileForm, 'surname')->input('text', ['id' => 'surname']); ?>
                <?=$form->field($UpdateUserProfileForm, 'name')?>
                <?=$form->field($UpdateUserProfileForm, 'patrinimic')?>
                <?= $form->field($UpdateUserProfileForm, 'sex')->radioList(['Мужской', 'Женский'], [
                    'itemOptions' => [
                        'id' => 'sex',
                        'disabled' => true
                    ]
                ])?>
                <?= $form->field($UpdateUserProfileForm, 'email') ->input('email')?>
                <?= $form->field($UpdateUserProfileForm, 'phone2')
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

                <?= $form->field($UpdateUserProfileForm, 'email2') ->input('email')?>

                <?=
                $form->field($UpdateUserProfileForm, 'bithday')
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
                <?= $form->field($UpdateUserProfileForm, 'photo')->fileInput([
                    'id' => 'pathPhoto',
                    'disabled' => true
                ]) ?>
                <?= Html::img($modelProfile->urlPhoto, ['id' => 'photoPreview', 'class' => 'profile_photo_min'])?>

            </div>
            <div class="col-lg-4" id="passport">
                <label class="h4">Паспорт и регистрация <?=
                    Html::a(Html::icon('edit', ['title' => 'Редактировать']), '#', ['id' => 'edit_passport']);
                    ?>
                </label>

                <br><br>
                <comment>Все личные данные клиентов хранятся в зашифрованном виде! Водители в первую очередь принимают заказы от Клиентов, с наиболее полной информацией в профиле (При аозможности
                    заполняйте все поля!) и наивысшим рейтингом.</comment>
                <br><br>
                <?= $form->field($UpdateUserProfileForm, 'country')->dropDownList(\yii\helpers\ArrayHelper::map(
                    (($q = new \yii\db\Query())
                        ->select(['id_country', 'name'])
                        ->from('country')
                        ->all()
                    ), 'id_country', 'name'
                ), [
                    'id' => 'countryPassportDwnList',
                    'class' => 'btn btn-primary'
                ] )?>
                <?= $form->field($UpdateUserProfileForm, 'passport_number')
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
                <?= $form->field($UpdateUserProfileForm, 'passport_date')
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
                <?= $form->field($UpdateUserProfileForm, 'passport_place')->textarea(['placeholder'=>'Кем выдан'])
                ?>
                <?= $form->field($UpdateUserProfileForm, 'reg_address')->textarea()?>

            </div>
            <div class="col-lg-4">

            </div>
            <div class="col-lg-12">
                <br><br>
                <?=Html::submitButton('Сохранить',
                    [
                        'class' => 'btn btn-success btn-block',
                        'disabled' => true
                    ])?>            </div>
            <?php $form::end(); ?>
        </div>

    </div>

<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<?php
    foreach (Yii::$app->authManager->getRolesByUser(Yii::$app->user->id) as $role){
        echo $role->name . '<br>';
    }
    var_dump(Yii::$app->user->can('car_owner'));
?>
<br>
<?//= var_dump($_SERVER)?>
    <iframe frameborder="0" src="https://pushall.ru/widget.php?subid=4781&type=middle" width="420" height="110" scrolling="no" style="overflow: hidden;">
    </iframe>
<br>
<?php
    $url = Yii::$app->urlManager->createAbsoluteUrl([
        '//https://pushall.ru/api.php',
        'type' => 'broadcast',
        'id' => '4781',
        'key' => 'fbbc4ea3fbe1cdb2f7fc1b4246d48174',
        'title' => 'test1',
        'text' => 'TEST TEST',
        'url' => 'http://2.grigorov.org/order/vehicle',
        'priority' => '1'
    ], 'https');
?>
<?= $url?>
<?= Html::a('Отправить',
        $url,  ['class' => 'btn btn-primary'])?>
<br>
<?= Yii::getAlias('@app')?>
<br>
<?= $modelUser->id;?>
<br>
<?= Yii::$app->user->can('user') . ' '  .key(Yii::$app->authManager->getRolesByUser($modelUser->id))?>
<!--<?//= $modelUser->profile->getRolesToString()?>-->
    <br>
<?= $modelUser->username?>
    <br>
<?= $modelUser->email?>
    <br>
<?= $modelUser->created_at?>
    <br>
<?= ($modelUser->profile->name)?>
    <br>
<?= $modelUser->profile->surname?>
    <br>
<?= $modelUser->profile->patrinimic?>
    <br>
<?= $modelUser->profile->getSex()?>
<br>
<img src="<?=$modelUser->profile->urlPhoto?>" style="width: auto; height: 100px"/>
    <br>

<br>
<?php
    if($modelUser->profile->passport):
?>
<?= $modelUser->profile->passport->number?>
<br>
<?= $modelUser->profile->passport->date?>
<br>
<?= $modelUser->profile->passport->place?>
<br>
<?= $modelUser->profile->passport->country?>
<br>
<?php
    endif;
?>
<?php

    var_dump(($modelUser->push_ids));
?>

