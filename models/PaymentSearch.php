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
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type', 'date', 'id_payer_user', 'id_recipient_user', 'id_payer_company', 'id_recipient_company', 'status', 'create_at', 'update_at'], 'integer'],
            [['cost'], 'number'],
            [['comments', 'sys_info'], 'safe'],
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
            'cost' => $this->cost,
            'type' => $this->type,
            'date' => $this->date,
            'id_payer_user' => $this->id_payer_user,
            'id_recipient_user' => $this->id_recipient_user,
            'id_payer_company' => $this->id_payer_company,
            'id_recipient_company' => $this->id_recipient_company,
            'status' => $this->status,
            'create_at' => $this->create_at,
            'update_at' => $this->update_at,
        ]);

        $query->andFilterWhere(['like', 'comments', $this->comments])
            ->andFilterWhere(['like', 'sys_info', $this->sys_info]);

        return $dataProvider;
    }
}
