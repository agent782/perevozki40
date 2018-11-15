<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Driver;

/**
 * DriverSearch represents the model behind the search form about `app\models\Driver`.
 */
class DriverSearch extends Driver
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'birthday', 'passport_id', 'license_id', 'raiting', 'checking', 'create_at', 'status'], 'integer'],
            [['name', 'surname', 'patronymic', 'address', 'phone', 'phone2'], 'safe'],
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
        $query = Driver::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'status' => SORT_DESC,
                    'surname' => SORT_ASC,
                    'name' => SORT_ASC,
                    'patronymic' => SORT_ASC,
                ],
            ],
        ]);
//        $dataProvider->query->andFilterWhere(['status'=>1]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'birthday' => $this->birthday,
            'passport_id' => $this->passport_id,
            'license_id' => $this->license_id,
            'raiting' => $this->raiting,
            'checking' => $this->checking,
            'create_at' => $this->create_at,
            'status' => $this->status
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'surname', $this->surname])
            ->andFilterWhere(['like', 'patronymic', $this->patronymic])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'phone2', $this->phone2]);

        return $dataProvider;
    }
}
