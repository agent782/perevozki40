<?php

namespace app\models;

use app\components\widgets\ShowMessageWidget;
use Yii;
use yii\base\Model;

/**
 * This is the model class for table "tip".
 *
 * @property int $id
 * @property string $model
 * @property string $attribute
 * @property string $description
 */
class Tip extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tip';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description'], 'safe'],
            [['model', 'attribute'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model' => 'Model',
            'attribute' => 'Attribute',
            'description' => 'Description',
        ];
    }

    static public function getTipButtonModal($model, string $attr){
        $Model = '';
        if(is_string($model)){
            $Model = $model;
        } else {
            $Model = $model->formName();
        }
        return
            ShowMessageWidget::widget([
               'helpMessage' => self::findOne(['model' => $Model, 'attribute' => $attr])->description,
                'header' => 'Подсказка'
            ]);

    }
}
