<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 10.08.2017
 * Time: 12:44
 */

namespace app\models;
use yii\base\Model;
use yii\db\ActiveRecord;
use Yii;

class auth_item extends Model
{
    public $name;
    public function attributeLabels()
    {
        return [
            'name' => 'Имя роли',
        ];
    }

    public function rules()
    {
        return [
            ['name', 'required'],
        ];
    }

    public function AddRole($role){
        $auth = Yii::$app->authManager;
        if($auth->getRole($role)) {
            return NULL;
        }
        $_role = $auth->createRole($role);
        return $auth->add($_role);
    }

}