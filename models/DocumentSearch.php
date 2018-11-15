<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Document;

/**
 * DocumentSearch represents the model behind the search form about `app\models\Document`.
 */
class DocumentSearch extends Document
{

    public $companyName;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'date', 'type', 'status', 'id_company', 'id_vehicle', 'id_user'], 'integer'],
            [['url_download', 'url_upload', 'comments', 'companyName'], 'safe'],
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
        $query = Document::find()
            ->joinWith(['company'])
        ;

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
            'documents.id' => $this->id,
            'documents.date' => $this->date,
            'documents.type' => $this->type,
            'documents.status' => $this->status,
            'documents.id_company' => $this->id_company,
            'documents.id_vehicle' => $this->id_vehicle,
            'documents.id_user' => $this->id_user,
//            'typeString' => $this->typeString,
//            'statusString' => $this->statusString,
        ]);

        $query
            ->andFilterWhere(['like', 'documents.url_download', $this->url_download])
            ->andFilterWhere(['like', 'documents.url_upload', $this->url_upload])
            ->andFilterWhere(['like', 'documents.comments', $this->comments])
            ->andFilterWhere(['like', 'company.name', $this->companyName]);

//        $query->joinWith(['company' => function ($q) {
//            $q->where('company.name LIKE "%' . $this->companyName . '%"');
//        }]);
        return $dataProvider;
    }


}
