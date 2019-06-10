<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 31.05.2019
 * Time: 11:32
 */

namespace app\components;


use app\models\Tip;
use yii\bootstrap\ActiveField;

class myActiveField extends ActiveField
{
    public function label($label = null, $options = [])
    {
        if(array_key_exists('withTip',$options)) {
            if($options['withTip']) {
                $label = $label . Tip::getTipButtonModal($this->model, $this->attribute);
            }
        }
        if (is_bool($label)) {
            $this->enableLabel = $label;
            if ($label === false && $this->form->layout === 'horizontal') {
                Html::addCssClass($this->wrapperOptions, $this->horizontalCssClasses['offset']);
            }
        } else {
            $this->enableLabel = true;
            $this->renderLabelParts($label, $options);
            parent::label($label, $options);
        }
        return $this;
    }

}