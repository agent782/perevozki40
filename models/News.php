<?php

namespace app\models;

use Yii;
use app\components\DateBehaviors;
use app\components\SerializeBehaviors;


/**
 * This is the model class for table "news".
 *
 * @property int $id
 * @property int $id_category
 * @property string $title
 * @property string $description
 * @property string $text
 * @property int $ratingUp
 * @property int $ratingDown
 * @property int $views
 * @property int $create_at
 * @property int $update_at
 */
class News extends \yii\db\ActiveRecord
{
    const CATEGORY_FOR_ALL = 0;
    const CATEGORY_FOR_USER = 1;
    const CATEGORY_FOR_CAR_OWNER = 2;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'news';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_category', 'title', 'text', 'description'], 'required'],
            [['id_category'], 'integer'],
            [['text', 'description'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['create_at', 'update_at'], 'default', 'value' => date('d.m.Y')],
            [['views', 'rating_up', 'rating_down'] , 'default', 'value' => 0],
            [['rating_up', 'rating_down'] , 'safe']

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_category' => 'Раздел',
            'title' => 'Тема',
            'description' => 'Description',
            'text' => '',
            'rating_up' => '',
            'rating_down' => '',
            'views' => 'Просмотры',
            'create_at' => 'Дата',
            'update_at' => '',
        ];
    }

    public function behaviors()
    {
        return [
            'convertDate' => [
                'class' => DateBehaviors::class,
                'dateAttributes' => [
                    'create_at',
                    'update_at',
                ],
                'format' => DateBehaviors::FORMAT_DATE,
            ],
            'SerializeUnserialize' => [
                'class' => SerializeBehaviors::class,
                'arrAttributes' => ['rating_up', 'rating_down'],
            ]
        ];
    }

    public function getCategory_ids_array(){
        return [
            '0' => 'Для всех',
            '1' => 'Для зарегистрированных',
            '2' => 'Для владельцев ТС'
        ];
    }

    public function PublicNews(){
        if(Yii::$app->user->can('admin')) return true;
        switch ($this->id_category){
            case self::CATEGORY_FOR_ALL:
                if(!Yii::$app->user->isGuest) return true;
//                return true;
                break;
            case self::CATEGORY_FOR_USER:
                if(Yii::$app->user->can('@')) return true;
                break;
            case self::CATEGORY_FOR_CAR_OWNER:
                break;
        }
        return false;
    }

    public function getRatingUp(){
        if($this->rating_up && is_array($this->rating_up)){
            return count($this->rating_up);
        }
        return 0;
    }

    public function getRatingDown(){
        if($this->rating_down && is_array($this->rating_down)){
            return count($this->rating_down);
        }
        return 0;
    }
}
