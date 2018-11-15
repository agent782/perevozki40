<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PriceZone;

/**
 * PriceZoneSearch represents the model behind the search form about `app\models\PriceZone`.
 */
class PriceZoneSearch extends PriceZone
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'veh_type', 'longlength', 'passengers', 'r_loading'], 'integer'],
            [['body_types'], 'safe'],
            [['tonnage_min', 'tonnage_max', 'volume_min', 'volume_max', 'length_min', 'length_max', 'tonnage_long_min', 'tonnage_long_max', 'length_long_min',
                'length_long_max', 'tonnage_spec_min', 'tonnage_spec_max', 'length_spec_min', 'length_spec_max', 'volume_spec', 'r_km', 'h_loading', 'min_price', 'r_h', 'min_r_10',
                'min_r_20', 'min_r_30', 'min_r_40', 'min_r_50', 'remove_awning'], 'number'],
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
    public function search($params, $VehType, $sort)
    {
        $query = PriceZone::find()->where(['veh_type' => $VehType]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => $sort
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
            'veh_type' => $this->veh_type,
            'longlength' => $this->longlength,
            'tonnage_min' => $this->tonnage_min,
            'tonnage_max' => $this->tonnage_max,
            'volume_min' => $this->volume_min,
            'volume_max' => $this->volume_max,
            'length_min' => $this->length_min,
            'length_max' => $this->length_max,
            'tonnage_long_min' => $this->tonnage_long_min,
            'tonnage_long_max' => $this->tonnage_long_max,
            'length_long_min' => $this->length_long_min,
            'length_long_max' => $this->length_long_max,
            'passengers' => $this->passengers,
            'tonnage_spec_min' => $this->tonnage_spec_min,
            'tonnage_spec_max' => $this->tonnage_spec_max,
            'length_spec_min' => $this->length_spec_min,
            'length_spec_max' => $this->length_spec_max,
            'volume_spec' => $this->volume_spec,
            'r_km' => $this->r_km,
            'h_loading' => $this->h_loading,
            'r_loading' => $this->r_loading,
            'min_price' => $this->min_price,
            'r_h' => $this->r_h,
            'min_r_10' => $this->min_r_10,
            'min_r_20' => $this->min_r_20,
            'min_r_30' => $this->min_r_30,
            'min_r_40' => $this->min_r_40,
            'min_r_50' => $this->min_r_50,
            'remove_awning' => $this->remove_awning,
        ]);

        $query->andFilterWhere(['like', 'body_types', $this->body_types]);
        return $dataProvider;
    }
}
