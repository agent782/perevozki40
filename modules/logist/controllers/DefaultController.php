<?php

namespace app\modules\logist\controllers;

use app\models\Order;
use app\models\Payment;
use yii\web\Controller;

/**
 * Default controller for the `logist` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $OrderBank = Order::find()
            ->where(['status' => Order::STATUS_CONFIRMED_VEHICLE])
            ->andWhere(['type_payment' => Payment::TYPE_BANK_TRANSFER])
            ->sum('cost_finish');
        $queryOrderCash = Order::find()->where(['type_payment' => Payment::TYPE_CASH]);

        $OrdersBankPaid = Order::find()
            ->where(['type_payment' => Payment::TYPE_BANK_TRANSFER])
            ->andWhere(['status' => Order::STATUS_CONFIRMED_VEHICLE])
            ->andWhere(['paid_status' => Order::PAID_YES])
            ->sum('cost_finish');
        $OrdersBankNotPaid = Order::find()
            ->where(['type_payment' => Payment::TYPE_BANK_TRANSFER])
            ->andWhere(['status' => Order::STATUS_CONFIRMED_VEHICLE])
            ->andWhere(['paid_status' => Order::PAID_NO])
            ->sum('cost_finish');

//        return
//            $OrderBank . ' ' .
//            $OrdersBankPaid
//            . ' ' .
//            $OrdersBankNotPaid
//            ;
        return $this->render('index');
    }
}
