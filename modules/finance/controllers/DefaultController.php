<?php

namespace app\modules\finance\controllers;

use app\models\Document;
use app\models\Order;
use app\models\Payment;
use app\models\RequestPayment;
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
        $count_request_payment = self::getCountRequestPayment();

        return $this->render('index', [
            'count_outstanding_invoices' => $count_outstanding_invoices,
            'count_outstanding_certificates' => $count_outstanding_certificates,
            'count_request_payment' => $count_request_payment
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

    protected function getCountRequestPayment(){
        return RequestPayment::find()
            ->where(['status' => RequestPayment::STATUS_NEW])
            ->count();

    }

}
