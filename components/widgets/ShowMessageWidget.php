<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 30.03.2018
 * Time: 13:26
 */

namespace app\components\widgets;


use yii\base\Widget;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;

class ShowMessageWidget extends Widget
{
    public $ToggleButton = ['label' => '<img src="/img/icons/info-25.png">', 'class' => 'btn'];
    public $helpMessage;
    public $header = 'Информация';


    public function run()
    {
        Modal::begin([
            'toggleButton' => $this->ToggleButton,
            'header' => $this->header,
            'closeButton' => ['hidden' => false],
//            'bodyOptions' => ['class' => 'container'],
        ]);

        echo $this->helpMessage;

        Modal::end();
    }
}
