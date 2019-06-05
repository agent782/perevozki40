<?php

/* @var User $modelUser*/
/* @var Profile $modelProfile*/
/* @var UpdateUserProfileForm $UpdateUserProfileForm*/
/* @var \yii\web\View $this*/

    $this->registerJsFile('/js/updateUserProfileForm.js');
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


//    $this->title = Html::encode(
//             $modelProfile->fioFull). Html::a(' (Выйти)', '/default/logout', ['style' => 'font-size: 12px;']);
?>

    <div class="container" id="profile">
        <label class="h4"><?= $this->title?></label>

        <?php
            $form = \yii\bootstrap\ActiveForm::begin([
                'fieldConfig' => [
                    'inputOptions' => [
                        'style' => 'width:auto',
                        'readonly' => true
                    ]
                ]
            ]);
        ?>
        <?=
            $form->field($UpdateUserProfileForm, 'surname')->input('text', ['id' => 'surname'])
                ->label($modelProfile->getAttributeLabel('surname')
                    . ' ' .Html::a(Html::icon('edit', ['title' => 'Редактировать']), '#', ['id' => 'edit_surname']))

        ?>
        <?=$form->field($UpdateUserProfileForm, 'name')?>
        <?=$form->field($UpdateUserProfileForm, 'patrinimic')?>
        <?php
            $form::end();
        ?>


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
<?= $modelUser->profile->bithday?>
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