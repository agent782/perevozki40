<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 16.01.2018
 * Time: 13:43
 */

namespace app\models;
use Yii;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;

class UserSearch extends User
{
    public function rules()
    {
        // только поля определенные в rules() будут доступны для поиска
        return [
            [['id'], 'integer'],
            [['username', 'dateCreatedAt'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = User::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // загружаем данные формы поиска и производим валидацию
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        // изменяем запрос добавляя в его фильтрацию
        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['like', 'username', $this->username]);
//            ->andFilterWhere(['like', 'creation_date', $this->DateCreatedAt]);

        return $dataProvider;
    }

}