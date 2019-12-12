<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Company;
use yii\data\ArrayDataProvider;

/**
 * CompanySearch represents the model behind the search form about `app\models\Company`.
 */
class CompanySearch extends Company
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'kpp', 'ogrn_date', 'state_actuality_date', 'state_registration_date',
                'state_liquidation_date', 'data_type', 'status', 'raiting', 'created_at', 'updated_at'], 'integer'],
            [['inn', 'name', 'address', 'address_real', 'address_post', 'value', 'address_value',
                'branch_type', 'capital', 'email', 'email2', 'email3', 'management_name', 'management_post',
                'name_full', 'name_short', 'ogrn', 'okpo', 'okved', 'opf_short', 'phone', 'phone2', 'phone3',
                'citizenship', 'state_status', 'FIO_contract', 'basis_contract', 'job_contract'], 'safe'],
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
    public  function searchProfileAll($params){

        $query = Profile::findOne(Yii::$app->user->getId())->getCompanies();

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
        return $dataProvider;
    }

    public function searchAll($params)
    {
        $query = Company::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'attributes' => [
                    ''
                ],
                'defaultOrder' => [
                    'updated_at' => SORT_DESC,
                ]
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
            'kpp' => $this->kpp,
            'ogrn_date' => $this->ogrn_date,
            'state_actuality_date' => $this->state_actuality_date,
            'state_registration_date' => $this->state_registration_date,
            'state_liquidation_date' => $this->state_liquidation_date,
            'data_type' => $this->data_type,
            'status' => $this->status,
            'raiting' => $this->raiting,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'inn', $this->inn])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'address_real', $this->address_real])
            ->andFilterWhere(['like', 'address_post', $this->address_post])
            ->andFilterWhere(['like', 'value', $this->value])
            ->andFilterWhere(['like', 'address_value', $this->address_value])
            ->andFilterWhere(['like', 'branch_type', $this->branch_type])
            ->andFilterWhere(['like', 'capital', $this->capital])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'email2', $this->email2])
            ->andFilterWhere(['like', 'email3', $this->email3])
            ->andFilterWhere(['like', 'management_name', $this->management_name])
            ->andFilterWhere(['like', 'management_post', $this->management_post])
            ->andFilterWhere(['like', 'name_full', $this->name_full])
            ->andFilterWhere(['like', 'name_short', $this->name_short])
            ->andFilterWhere(['like', 'ogrn', $this->ogrn])
            ->andFilterWhere(['like', 'okpo', $this->okpo])
            ->andFilterWhere(['like', 'okved', $this->okved])
            ->andFilterWhere(['like', 'opf_short', $this->opf_short])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'phone2', $this->phone2])
            ->andFilterWhere(['like', 'phone3', $this->phone3])
            ->andFilterWhere(['like', 'citizenship', $this->citizenship])
            ->andFilterWhere(['like', 'state_status', $this->state_status])
            ->andFilterWhere(['like', 'FIO_contract', $this->FIO_contract])
            ->andFilterWhere(['like', 'basis_contract', $this->basis_contract])
            ->andFilterWhere(['like', 'job_contract', $this->job_contract]);

        return $dataProvider;
    }

    public function searchAllArray(){
        $query = Company::find();

        return new ArrayDataProvider([
           'allModels' => $query->all(),
            'pagination' => [
                'pageSize' => 100,
            ],
            'sort' => [
                'attributes' => [
                    'balanceSum'
                ],
                'defaultOrder' => [
                    'balanceSum' => SORT_ASC
                ]
            ]
        ]);

    }
}
