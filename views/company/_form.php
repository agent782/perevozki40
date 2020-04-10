<?php

use yii\widgets\MaskedInput;
$this->registerCssFile("https://cdn.jsdelivr.net/npm/suggestions-jquery@17.10.1/dist/css/suggestions.min.css");
$this->registerJsFile("https://cdn.jsdelivr.net/npm/suggestions-jquery@17.10.1/dist/js/jquery.suggestions.min.js");
$this->registerJsFile('/js/jquery-dateFormat.js');
$this->registerJsFile('/js/addCompany.js');
/* @var $this yii\web\View */
/* @var $model app\models\Company */
/* @var $form yii\widgets\ActiveForm */
?>

    <?=
            $form->field($modelCompany, 'name')
                ->input('text', ['id' => 'Label', 'placeholder' => 'Введите ИНН или название:', 'autofocus' => true,
            'autocomplete' => 'on']);

    ?>


        <?= $form->field($XcompanyXprofile, 'job_post')->input('text', ['placeholder' => 'Менеджер по логистике'])?>
        <?= $form->field($modelCompany, 'phone')
//            ->input('tel', ['id' => 'phone'])
            ->widget(MaskedInput::class,[
//                'id' => 'phone',
                'mask' => '+7(999)999-99-99',
                'clientOptions'=>[
                    'removeMaskOnSubmit' => true,
                    'removeMaskOnSubmit' => true,
                ],
                'options' => [
                    'id' => 'phone',
                    'type' => 'tel',
                    'autocorrect' => 'off',
            ]
        ])
        ?>
        <?= $form->field($modelCompany, 'email')->input('text', ['id' => 'email'])?>
        <?= $form->field($modelCompany, 'address')->input('text', ['id' => 'address'])?>
        <?= $form->field($modelCompany, 'address_real')->input('text', ['id' => 'address_real',])?>
        <?= $form->field($modelCompany, 'address_post')->input('text', ['id' => 'address_post',])?>
        <?= $form->field($modelCompany, 'inn')->input('text', ['id' => 'inn'])?>
        <?= $form->field($modelCompany, 'kpp')->input('text', ['id' => 'kpp'])?>
        <?= $form->field($modelCompany, 'management_name')->input('text', ['id' => 'management-name'])?>
        <?= $form->field($modelCompany, 'management_post')->input('text', ['id' => 'management-post'])?>
        <?= $form->field($modelCompany, 'ogrn')->input('text',['id' => 'ogrn'])?>
        <?= $form->field($modelCompany, 'ogrn_date')
            ->input('text', ['id' => 'ogrn_date', 'placeholder' => '01.01.2000',])
//    ->widget(MaskedInput::class,[
//        'mask' => '99.99.9999',
//        'options' => [
//            'type' => 'tel',
//            'placeholder' => '31.01.2000'
//        ]
//    ])

        ?>
        <?= $form->field($modelCompany, 'FIO_contract')->input('text', ['id' => 'FIO_contract'])?>
        <?= $form->field($modelCompany, 'job_contract')->input('text', ['id' => 'job_contract'])?>
        <?= $form->field($modelCompany, 'basis_contract')->input('text', ['id' => 'basis_contract'])?>
        <?= $form->field($modelCompany, 'phone2')->input('text', ['id' => 'phone2'])?>
        <?= $form->field($modelCompany, 'email2')->input('text', ['id' => 'email2'])?>
        <?= $form->field($modelCompany, 'phone3')->input('text', ['id' => 'phone3'])?>
        <?= $form->field($modelCompany, 'email3')->input('text', ['id' => 'email3'])?>

        <?= $form->field($modelCompany, 'value')->hiddenInput(['id' => 'value'])->label(false)?>
        <?= $form->field($modelCompany, 'okved')->hiddenInput(['id' => 'okved'])->label(false)?>
        <?= $form->field($modelCompany, 'okpo')->hiddenInput(['id' => 'okpo'])->label(false)?>
        <?= $form->field($modelCompany, 'citizenship')->hiddenInput(['id' => 'citizenship'])->label(false)?>
        <?= $form->field($modelCompany, 'name_full')->hiddenInput(['id' => 'name-full'])->label(false)?>
        <?= $form->field($modelCompany, 'name_short')->hiddenInput(['id' => 'name-short'])->label(false)?>
        <?= $form->field($modelCompany, 'address_value')->hiddenInput(['id' => 'address-value'])->label(false)?>
        <?= $form->field($modelCompany, 'branch_type')->hiddenInput(['id' => 'branch_type'])->label(false)?>
        <?= $form->field($modelCompany, 'capital')->hiddenInput(['id' => 'capital'])->label(false)?>
        <?= $form->field($modelCompany, 'opf_short')->hiddenInput(['id' => 'opf-short'])->label(false)?>
        <?= $form->field($modelCompany, 'state_actuality_date')->hiddenInput(['id' => 'state-actuality_date'])->label(false)?>
        <?= $form->field($modelCompany, 'state_registration_date')->hiddenInput(['id' => 'state-registration_date'])->label(false)?>
        <?= $form->field($modelCompany, 'state_liquidation_date')->hiddenInput(['id' => 'state-liquidation_date'])->label(false)?>
        <?= $form->field($modelCompany, 'state_status')->hiddenInput(['id' => 'state-status'])->label(false)?>
        <?= $form->field($modelCompany, 'data_type')->hiddenInput(['id' => 'data-type'])->label(false)?>


