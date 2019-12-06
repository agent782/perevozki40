<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Vehicle;

/**
 * VehicleSearch represents the model behind the search form about `app\models\Vehicle`.
 */
class VehicleSearch extends Vehicle
{

    public $body_typies = [
//        Vehicle::BODY_manipulator, Vehicle::BODY_dump, Vehicle::BODY_crane, Vehicle::BODY_excavator, Vehicle::BODY_excavator_loader
    ];
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_user', 'tonnage', 'length', 'width', 'height', 'longlength', 'passengers', 'ep', 'rp', 'lp'], 'integer'],
            ['body_typies', 'safe']
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
    public function search($params, $VehType, $sort, array $Statuses, bool $admin = false)
    {
        $id_user = Yii::$app->user->id;
        if(Yii::$app->user->can('admin')
            || Yii::$app->user->can('dispetcher')
        ){
            $query = Vehicle::find()->where(['id_vehicle_type' => $VehType, 'status' => $Statuses]);
        }else {
            $query = Vehicle::find()->where(['id_user' => $id_user, 'id_vehicle_type' => $VehType, 'status' => $Statuses]);
        }
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => $sort,
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
            'tonnage' => $this->tonnage,
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height,
            'longlength' => $this->longlength,
            'passengers' => $this->passengers,
            'ep' => $this->ep,
            'rp' => $this->rp,
            'lp' => $this->lp,
        ]);
        $query->andFilterWhere(['in', 'body_type', $this->body_typies]);

        return $dataProvider;
    }


}
