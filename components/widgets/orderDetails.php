<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 20.11.2017
 * Time: 17:30
 */
namespace app\components\widgets;
use app\models\Order;
use yii\base\Widget;
use yii\helpers\Html;
class OrderDetails extends Widget
{
    public $order;
    public $route;
    public function init()
    {
        parent::init();
        if ($this->order === null) {
            $this->order = new Order();
        }
    }
    public function run()
    {
        return Html::encode($this->order->id);
    }
}