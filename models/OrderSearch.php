<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Order;
use yii\data\ArrayDataProvider;

/**
 * OrderSearch represents the model behind the search form about `app\models\Order`.
 */
class OrderSearch extends Order
{
    public $type_payments = [Payment::TYPE_BANK_TRANSFER];

    public $statuses = [Order::STATUS_NEW, Order::STATUS_IN_PROCCESSING, Order::STATUS_VEHICLE_ASSIGNED,
        Order::STATUS_CONFIRMED_CLIENT, Order::STATUS_CONFIRMED_VEHICLE];


    public $invoiceNumber;
    public $certificateNumber;
    public $companyName;
    public $hasInvoice;
    public $hasCertificate;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_vehicle_type', 'longlength', 'passengers', 'ep', 'rp', 'lp', 'datetime_start',
                'datetime_finish', 'datetime_access', 'valid_datetime', 'id_route', 'id_route_real','type_payment', 'id_company'], 'integer'],
            [['tonnage', 'length', 'width', 'height', 'volume', 'tonnage_spec', 'length_spec', 'volume_spec'], 'number'],
            [['cargo', 'statuses', 'type_payments'], 'safe'],
            [['invoiceNumber', 'certificateNumber', 'companyName', 'paid_status', 'paid_car_owner_status',
                'hasInvoice', 'hasCertificate'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Order::find();
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'datetime_start' => SORT_DESC
                ]
            ],
            'pagination' => [
                'pageSize' => 20
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            Order::tableName().'.id' => $this->id,
        ]);
        $query->andFilterWhere(['IN', 'type_payment', $this->type_payments]);
        $query->andFilterWhere(['IN', 'paid_status', $this->paid_status]);
        $query->andFilterWhere(['IN', 'paid_car_owner_status', $this->paid_car_owner_status]);

// Если фильтр пуст, показывать такде все заказы, которые не имеют счет
        if($this->invoiceNumber) {
            $query->joinWith(['invoice' => function ($q) {
                $q->andWhere('invoice.number LIKE "%' . $this->invoiceNumber . '%"');
            }]);
        }
//         Если фильтр пуст, показывать такде все заказы, которые не имеют акт
        if($this->certificateNumber) {
            $query->joinWith(['certificate' => function ($q) {
                $q->andWhere('invoice.number LIKE "%' . $this->certificateNumber . '%"');
            }]);
        }
        if($this->companyName) {
            $query->joinWith(['company' => function ($q) {
                $q->andWhere('company.name LIKE "%' . $this->companyName . '%"');
            }]);
        }

        if($this->hasInvoice) {
            $ids_order_need_invoice = [];
            foreach (Order::find()->where(['type_payment' => Payment::TYPE_BANK_TRANSFER])->all() as $order){
                if(!$order->invoice){
                    $ids_order_need_invoice[] = $order->id;
                }
            }
            $query->filterWhere(['in', 'id', $ids_order_need_invoice]);
        }

        if($this->hasCertificate) {
            $ids_order_need_certificate = [];
            foreach (Order::find()->where(['type_payment' => Payment::TYPE_BANK_TRANSFER])->all() as $order){
                if(!$order->certificate){
                    $ids_order_need_certificate[] = $order->id;
                }
            }
            $query->filterWhere(['in', 'id', $ids_order_need_certificate]);
        }
        return $dataProvider;
    }

    public function searchForClientNEWOrders($params)
    {
        $query = Order::find()
            ->where(['status' => Order::STATUS_NEW])
            ->orWhere(['status' => Order::STATUS_EXPIRED])
            ->orWhere(['status' => Order::STATUS_IN_PROCCESSING])
            ->andWhere(['id_user' => Yii::$app->user->id])
        ;

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);
        $dataProvider->setSort([

            'defaultOrder' => [
                'datetime_start' => SORT_DESC
            ]
        ]);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,

        ]);

        return $dataProvider;
    }

    public function searchCanVehicle($params)
    {
        $vehicles = Vehicle::find()->where(['in','status', [Vehicle::STATUS_ACTIVE, Vehicle::STATUS_ONCHECKING]])
            ->andWhere(['id_user' => Yii::$app->user->id])
            ->all();
        $orders = [];
        $Orders = Order::find()
            ->where(['in', 'status' , [Order::STATUS_NEW, Order::STATUS_IN_PROCCESSING]])
            ->andWhere(['in','hide', [false, null]])
//            ->asArray();
            ->all();
        foreach ($Orders as $Order){
            foreach($vehicles as $vehicle){
                if ($vehicle->canOrder($Order)){
                    $orders [] = $Order;
                    break;
                }
            }
        }
        // add conditions that should always apply here

        $dataProvider = new ArrayDataProvider([
            'allModels' =>  $orders,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);
//        $dataProvider->setSort([
//
//            'defaultOrder' => [
//                'datetime_start' => SORT_DESC
//            ]
//        ]);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


        // grid filtering conditions
//        $orders->andFilterWhere([
//            'id' => $this->id,
//            'id_vehicle_type' => $this->id_vehicle_type,
//            'tonnage' => $this->tonnage,
//            'length' => $this->length,
//            'width' => $this->width,
//            'height' => $this->height,
//            'volume' => $this->volume,
//            'longlength' => $this->longlength,
//            'passengers' => $this->passengers,
//            'ep' => $this->ep,
//            'rp' => $this->rp,
//            'lp' => $this->lp,
//            'tonnage_spec' => $this->tonnage_spec,
//            'length_spec' => $this->length_spec,
//            'volume_spec' => $this->volume_spec,
//            'datetime_start' => $this->datetime_start,
//            'datetime_finish' => $this->datetime_finish,
//            'datetime_access' => $this->datetime_access,
//            'valid_datetime' => $this->valid_datetime,
//            'id_route' => $this->id_route,
//            'id_route_real' => $this->id_route_real,
//        ]);
//
//
//        $query->andFilterWhere(['OR LIKE', 'paid_status', $this->paid_statuses]);
////        $query->andFilterWhere(['like', 'cargo', $this->cargo]);
//        $query->andFilterWhere((['OR LIKE', 'status', $this->statuses]));

        return $dataProvider;
    }
}
