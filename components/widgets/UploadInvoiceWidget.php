<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 19.03.2018
 * Time: 14:33
 */

namespace app\components\widgets;

use app\models\Order;
use Yii;
use app\models\Document;
use app\models\Invoice;
use yii\base\Widget;
use yii\bootstrap\Modal;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\jui\DatePicker;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use yii\widgets\MaskedInput;


class UploadInvoiceWidget extends Widget
{
    public $modelInvoice;
    public $id_order;
    public $type_document;
    public $header = 'Загрузка документа';
    public $ToggleButton;
    public $action;
    public $fieldFileLable;
    public $index;


    public function run(){
        if($this->id_order)
            self::uploadToClient();
    }

    private function uploadToClient(){
        Modal::begin([
            'header' => $this->header,
            'toggleButton' => $this->ToggleButton,
//            'id' => 'modal_' .  rand()
        ]);
//var_dump($this->modelInvoice);
        $form = ActiveForm::begin([
            'id' => 'form_'. $this->type_document .'_' . $this->index,
            'action' => $this->action,
            'enableAjaxValidation' => true,
            'enableClientValidation' => true,
            'validationUrl' => \yii\helpers\Url::to(['/finance/invoice/validate-date']),
            ]);

        echo $form->field($this->modelInvoice, 'upload_file')->fileInput([
            'id' => 'pathPhoto_'. $this->type_document .'_' . $this->index ,
        ])->label($this->fieldFileLable);

        echo $form->field($this->modelInvoice, 'number')->input('tel');
        echo $form->field($this->modelInvoice, 'date')
            ->widget(MaskedInput::class,[
            'mask' => '99.99.9999',
            'options' => [
                'aria-required' => false,
                'id' => 'mask_' . rand(),
            ],
        ])
        ;


        echo  Html::submitButton('Загрузить', ['class' => 'btn btn-primary']);
        ActiveForm::end();

        Modal::end();
    }
}