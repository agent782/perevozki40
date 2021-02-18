<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Profile;
use yii\data\ArrayDataProvider;

/**
 * ProfileSearch represents the model behind the search form of `app\models\Profile`.
 */
class ProfileSearch extends Profile
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_user', 'bithday', 'status_client', 'status_vehicle', 'raiting_client', 'raiting_vehicle', 'id_passport', 'is_driver', 'id_driver_license'], 'integer'],
            [['phone2', 'email2', 'name', 'surname', 'patrinimic', 'sex', 'photo', 'reg_address', 'old_id','balanceCarOwnerPayNow'], 'safe'],
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
        $query = Profile::find();

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
            'id_user' => $this->id_user,
            'bithday' => $this->bithday,
            'status_client' => $this->status_client,
            'status_vehicle' => $this->status_vehicle,
            'raiting_client' => $this->raiting_client,
            'raiting_vehicle' => $this->raiting_vehicle,
            'id_passport' => $this->id_passport,
            'is_driver' => $this->is_driver,
            'id_driver_license' => $this->id_driver_license,
        ]);

        $query->andFilterWhere(['like', 'phone2', $this->phone2])
            ->andFilterWhere(['like', 'email2', $this->email2])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'surname', $this->surname])
            ->andFilterWhere(['like', 'patrinimic', $this->patrinimic])
            ->andFilterWhere(['like', 'sex', $this->sex])
            ->andFilterWhere(['like', 'photo', $this->photo])
            ->andFilterWhere(['like', 'reg_address', $this->reg_address]);

        return $dataProvider;
    }

    public function searchCarOwners($params)
    {
//        $query = Profile::find()->joinWith('authAssignment')
//            ->andWhere(['auth_assignment.item_name' => 'car_owner'])
//        ;
        $query = [];
        $Profiles = Profile::find()->joinWith('user')->all();
        foreach ($Profiles as $profile){
            if(
                ($profile->getVehicles()->count()
                || $profile->user->canRole('car_owner'))
                && ($profile->balanceCarOwnerPayNow && $profile->balanceCarOwnerSum)
            ){
                $query [] = $profile;
            }
        }

        return new ArrayDataProvider([
            'allModels' => $query,
            'sort' => [
                'attributes' => [
                    'balanceCarOwnerPayNow',
                    'balanceCarOwnerSum'
                ],
                'defaultOrder' => [
                    'balanceCarOwnerPayNow' => SORT_DESC
                ]
            ],
            'pagination' => [
                'pageSize' => 100
            ]
        ]);

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
//                    'balanceCarOwnerPayNow'
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
            'id_user' => $this->id_user,
            'bithday' => $this->bithday,
            'status_client' => $this->status_client,
            'status_vehicle' => $this->status_vehicle,
            'raiting_client' => $this->raiting_client,
            'raiting_vehicle' => $this->raiting_vehicle,
            'id_passport' => $this->id_passport,
            'is_driver' => $this->is_driver,
            'id_driver_license' => $this->id_driver_license,
        ]);

        $query->andFilterWhere(['like', 'phone2', $this->phone2])
            ->andFilterWhere(['like', 'email2', $this->email2])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'surname', $this->surname])
            ->andFilterWhere(['like', 'patrinimic', $this->patrinimic])
            ->andFilterWhere(['like', 'sex', $this->sex])
            ->andFilterWhere(['like', 'photo', $this->photo])
            ->andFilterWhere(['like', 'reg_address', $this->reg_address]);

        return $dataProvider;
    }
}
