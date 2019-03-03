<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Route;

/**
 * RouteSearch represents the model behind the search form of `app\models\Route`.
 */
class RouteSearch extends Route
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'distance', 'count'], 'integer'],
            [['startCity', 'finishCity', 'routeStart', 'route1', 'route2', 'route3', 'route4', 'route5', 'route6', 'route7', 'route8', 'routeFinish'], 'safe'],
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
        $query = Route::find();

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
            'distance' => $this->distance,
            'count' => $this->count,
        ]);

        $query->andFilterWhere(['like', 'startCity', $this->startCity])
            ->andFilterWhere(['like', 'finishCity', $this->finishCity])
            ->andFilterWhere(['like', 'routeStart', $this->routeStart])
            ->andFilterWhere(['like', 'route1', $this->route1])
            ->andFilterWhere(['like', 'route2', $this->route2])
            ->andFilterWhere(['like', 'route3', $this->route3])
            ->andFilterWhere(['like', 'route4', $this->route4])
            ->andFilterWhere(['like', 'route5', $this->route5])
            ->andFilterWhere(['like', 'route6', $this->route6])
            ->andFilterWhere(['like', 'route7', $this->route7])
            ->andFilterWhere(['like', 'route8', $this->route8])
            ->andFilterWhere(['like', 'routeFinish', $this->routeFinish]);

        return $dataProvider;
    }
}
