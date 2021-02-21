<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 19.03.2018
 * Time: 14:33
 */

namespace app\components\widgets;


use app\models\Document;
use yii\base\Widget;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;

class ButtonUpload extends Widget
{
    public $model;
    public $attribute = 'upload_file';
    public $multiple = true;
    public $typeDocument;
    public $header = 'Отправка подписанного Договора';
    public $ToggleButton = ['label' => 'Отправить подписанный скан', 'class' => 'btn btn-xs btn-success'];
    public $completeRedirect = '/company/index';
    public $action;

    public function run()
    {
//        $modelDocument = Document::findOne(['id_company' => $this->idCompany, 'type'=>Document::TYPE_CONTRACT_CLIENT]);
        Modal::begin([
            'header' => $this->header,
            'toggleButton' => $this->ToggleButton,
        ]);

        $form = ActiveForm::begin([
            'action' => $this->action,
            ]);


        echo
        $form->field($this->model, $this->attribute)
            ->fileInput(['multiple' => $this->multiple])
            ->label(false)
        ;
        echo  Html::submitButton('Загрузить');
        ActiveForm::end();

        Modal::end();
    }
}