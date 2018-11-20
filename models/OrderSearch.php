<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Order;

/**
 * OrderSearch represents the model behind the search form about `app\models\Order`.
 */
class OrderSearch extends Order
{

    public $statuses = [Order::STATUS_NEW, Order::STATUS_IN_PROCCESSING, Order::STATUS_VEHICLE_ASSIGNED,
        Order::STATUS_CONFIRMED_CLIENT, Order::STATUS_CONFIRMED_VEHICLE];
    public $paid_statuses = [Order::PAID_NO];
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_vehicle_type', 'longlength', 'passengers', 'ep', 'rp', 'lp', 'datetime_start', 'datetime_finish', 'datetime_access', 'valid_datetime', 'id_route', 'id_route_real'], 'integer'],
            [['tonnage', 'length', 'width', 'height', 'volume', 'tonnage_spec', 'length_spec', 'volume_spec'], 'number'],
            [['cargo', 'statuses', 'paid_statuses'], 'safe'],
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
            'id_vehicle_type' => $this->id_vehicle_type,
            'tonnage' => $this->tonnage,
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height,
            'volume' => $this->volume,
            'longlength' => $this->longlength,
            'passengers' => $this->passengers,
            'ep' => $this->ep,
            'rp' => $this->rp,
            'lp' => $this->lp,
            'tonnage_spec' => $this->tonnage_spec,
            'length_spec' => $this->length_spec,
            'volume_spec' => $this->volume_spec,
            'datetime_start' => $this->datetime_start,
            'datetime_finish' => $this->datetime_finish,
            'datetime_access' => $this->datetime_access,
            'valid_datetime' => $this->valid_datetime,
            'id_route' => $this->id_route,
            'id_route_real' => $this->id_route_real,
        ]);

        $query->andFilterWhere(['like', 'cargo', $this->cargo]);

        return $dataProvider;
    }

    public function searchForClient($params)
    {
        $query = Order::find()->where(['id_user' => Yii::$app->user->id]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'id_vehicle_type' => $this->id_vehicle_type,
            'tonnage' => $this->tonnage,
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height,
            'volume' => $this->volume,
            'longlength' => $this->longlength,
            'passengers' => $this->passengers,
            'ep' => $this->ep,
            'rp' => $this->rp,
            'lp' => $this->lp,
            'tonnage_spec' => $this->tonnage_spec,
            'length_spec' => $this->length_spec,
            'volume_spec' => $this->volume_spec,
            'datetime_start' => $this->datetime_start,
            'datetime_finish' => $this->datetime_finish,
            'datetime_access' => $this->datetime_access,
            'valid_datetime' => $this->valid_datetime,
            'id_route' => $this->id_route,
            'id_route_real' => $this->id_route_real,
        ]);


        $query->andFilterWhere(['OR LIKE', 'paid_status', $this->paid_statuses]);
//        $query->andFilterWhere(['like', 'cargo', $this->cargo]);
        $query->andFilterWhere((['OR LIKE', 'status', $this->statuses]));

        return $dataProvider;
    }
}
