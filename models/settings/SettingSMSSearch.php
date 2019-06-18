<?php

namespace app\models\settings;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\settings\SettingSMS;

/**
 * SettingSMSSearch represents the model behind the search form of `app\models\settings\SettingSMS`.
 */
class SettingSMSSearch extends SettingSMS
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'last_num_contract', 'FLAG_EXPIRED_ORDER', 'sms_code_update_phone'], 'integer'],
            [['noPhotoPath'], 'safe'],
            [['user_discount_cash', 'client_discount_cash', 'vip_client_discount_cash', 'user_discount_card', 'client_discount_card', 'vip_client_discount_card', 'procent_vehicle', 'procent_vip_vehicle'], 'number'],
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
        $query = SettingSMS::find();

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
            'last_num_contract' => $this->last_num_contract,
            'FLAG_EXPIRED_ORDER' => $this->FLAG_EXPIRED_ORDER,
            'user_discount_cash' => $this->user_discount_cash,
            'client_discount_cash' => $this->client_discount_cash,
            'vip_client_discount_cash' => $this->vip_client_discount_cash,
            'user_discount_card' => $this->user_discount_card,
            'client_discount_card' => $this->client_discount_card,
            'vip_client_discount_card' => $this->vip_client_discount_card,
            'procent_vehicle' => $this->procent_vehicle,
            'procent_vip_vehicle' => $this->procent_vip_vehicle,
            'sms_code_update_phone' => $this->sms_code_update_phone,
        ]);

        $query->andFilterWhere(['like', 'noPhotoPath', $this->noPhotoPath]);

        return $dataProvider;
    }
}
