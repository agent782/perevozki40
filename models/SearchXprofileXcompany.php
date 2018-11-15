<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\XprofileXcompany;

/**
 * SearchXprofileXcompany represents the model behind the search form about `app\models\XprofileXcompany`.
 */
class SearchXprofileXcompany extends XprofileXcompany
{
    /**
     * @inheritdoc
     */
    public $companyName;
    public $fio;
//
    public function rules()
    {
        return [
            [['id_profile', 'id_company', 'term_of_office', 'checked', 'STATUS_POA'], 'integer'],
            [['job_post', 'url_form', 'url_upload_poa', 'url_poa', 'comments', 'companyName', 'surname', 'fio'], 'safe'],
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
        $query = XprofileXcompany::find()
            ->joinWith(['company', 'profile']);

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
            'id_profile' => $this->id_profile,
            'id_company' => $this->id_company,
            'term_of_office' => $this->term_of_office,
            'checked' => $this->checked,
            'STATUS_POA' => $this->STATUS_POA,
        ]);

        $query->andFilterWhere(['like', 'job_post', $this->job_post])
            ->andFilterWhere(['like', 'url_form', $this->url_form])
            ->andFilterWhere(['like', 'url_upload_poa', $this->url_upload_poa])
            ->andFilterWhere(['like', 'url_poa', $this->url_poa])
            ->andFilterWhere(['like', 'comments', $this->comments])
            ->andFilterWhere(['like', 'profile.surname', $this->fio])
            ->andFilterWhere(['like', 'company.name', $this->companyName]);

        return $dataProvider;
    }
}
