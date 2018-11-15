<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contract".
 *
 * @property integer $id
 * @property integer $date
 * @property string $url_form
 * @property string $url_pdf
 * @property integer $checked
 */
class Contract extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contract';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date', 'url_form'], 'required'],
            [['date', 'checked'], 'integer'],
            [['url_form', 'url_pdf'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'date' => Yii::t('app', 'Date'),
            'url_form' => Yii::t('app', 'Url Form'),
            'url_pdf' => Yii::t('app', 'Url Pdf'),
            'checked' => Yii::t('app', 'Checked'),
        ];
    }


}
