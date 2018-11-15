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
    public $typeDocument;
    public $ToggleButton = ['label' => 'Отправить подписанный скан', 'class' => 'btn btn-success'];
    public $completeRedirect = '/company/index';


    public function run()

    {
//        $modelDocument = Document::findOne(['id_company' => $this->idCompany, 'type'=>Document::TYPE_CONTRACT_CLIENT]);
        Modal::begin([
            'header' => 'Отправка подписанного Договора',
            'toggleButton' => $this->ToggleButton,
        ]);

        $form = ActiveForm::begin([
            'action' => \yii\helpers\Url::to(['document/upload', 'id' => $this->model->id, 'type' => $this->typeDocument, 'completeRedirect' => $this->completeRedirect]),
            ]);


        echo
        $form->field($this->model, 'upload_file[]')
            ->fileInput(['multiple' => true])
            ->label(false)
        ;
        echo  Html::submitButton('Загрузить');
        ActiveForm::end();

        Modal::end();
    }
}