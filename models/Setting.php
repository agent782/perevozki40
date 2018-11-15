<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "setting".
 *
 * @property integer $id
 * @property integer $last_num_contract
 * @property integer $id_our_company
 * @property string $noPhotoPath
 */
class Setting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'setting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'last_num_contract', 'id_our_company'], 'integer'],
            [['noPhotoPath'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'last_num_contract' => Yii::t('app', 'Last Num Contract'),
            'id_our_company' => Yii::t('app', 'Id Our Company'),
        ];
    }

    public static function getNoPhotoPath(){
        return Setting::find()->one()->noPhotoPath;
    }
}
