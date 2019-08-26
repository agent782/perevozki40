<?php

namespace app\modules\finance\controllers;

use app\models\Document;
use app\models\Order;
use app\models\Payment;
use yii\web\Controller;
use app\models\DocumentSearch;
use Yii;

/**
 * Default controller for the `finance` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $count_outstanding_invoices = self::getCountOutstandingInvoices();
        $count_outstanding_certificates = self::getCountOutstandingCertificate();

        return $this->render('index', [
            'count_outstanding_invoices' => $count_outstanding_invoices,
            'count_outstanding_certificates' => $count_outstanding_certificates
        ]);


    }

    protected function getCountOutstandingInvoices(){
        $count = 0;
        $orders = Order::find()
            ->where(['IN', 'status', [Order::STATUS_CONFIRMED_VEHICLE, Order::STATUS_CONFIRMED_CLIENT]])
            ->andWhere(['type_payment' => Payment::TYPE_BANK_TRANSFER])
            ->all();
        foreach ($orders as $order){
            if(!$order->invoice) $count++;
        }
        return $count;
    }
    protected function getCountOutstandingCertificate(){
        $count = 0;
        $orders = Order::find()
            ->where(['IN', 'status', [Order::STATUS_CONFIRMED_VEHICLE, Order::STATUS_CONFIRMED_CLIENT]])
            ->andWhere(['type_payment' => Payment::TYPE_BANK_TRANSFER])
            ->all();
        foreach ($orders as $order){
            if(!$order->certificate) $count++;
        }
        return $count;
    }

}
