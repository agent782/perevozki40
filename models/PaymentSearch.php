<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Payment;

/**
 * PaymentSearch represents the model behind the search form of `app\models\Payment`.
 */
class PaymentSearch extends Payment
{
    public $companyName;
    public $type_payments;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type', 'date', 'id_user', 'id_implementer', 'id_company', 'id_our_company', 'status', 'create_at', 'update_at'], 'integer'],
            [['cost'], 'number'],
            [['comments', 'sys_info', 'companyName', 'type_payments'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Payment::find();

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
            'id_user' => $this->id_user,
            'cost' => $this->cost,
//            'type' => $this->type,
//            'date' => $this->date,
            'status' => $this->status,
//            'create_at' => $this->create_at,
//            'update_at' => $this->update_at,
        ]);
        $query->andFilterWhere(['IN', 'type', $this->type_payments]);
        $query->andFilterWhere(['like', 'comments', $this->comments])
            ->andFilterWhere(['like', 'sys_info', $this->sys_info]);
        $query->joinWith(['company' => function($q){
            $q->andWhere('company.name LIKE "%'
                . $this->companyName
                . '%" OR company.name_short LIKE "%'
                . $this->companyName
                . '%"');
        }]);
        return $dataProvider;
    }
}
