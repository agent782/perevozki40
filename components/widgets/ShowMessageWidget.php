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
    public $ToggleButton = [];
    public $helpMessage;
    public $header = 'Информация';


    public function run()
    {   if(!$this->ToggleButton) {
            $this->ToggleButton = ['label' => Html::icon('info-sign'), 'style' => 'cursor: help;'];
        }
        Modal::begin([
            'toggleButton' => $this->ToggleButton,
            'header' => $this->header,
            'closeButton' => ['hidden' => false],
            'clientOptions' => [
                'backdrop' => 'static',
//                'keyboard' => true
            ],
            'bodyOptions' => [
//                'class' => 'container',
                'style' => '
                     position: relative;
                    overflow-y: auto;
                    max-height: 400px;
                    padding: 15px; '
            ],

        ]);

        echo '<div>' . $this->helpMessage . '</div>';

        Modal::end();
    }
}
