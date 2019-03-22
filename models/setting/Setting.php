<?php

namespace app\models\setting;

use app\models\SettingClient;
use Yii;

/**
 * This is the model class for table "setting".
 *
 * @property integer $id
 * @property integer $last_num_contract
 * @property integer $id_our_company
 * @property string $noPhotoPath
 * @property integer $FLAG_EXPIRED_ORDER
 */
class Setting extends \yii\db\ActiveRecord
{
    const TYPE_SETTING = 1;
    const TYPE_SETTING_CLIENT = 2;
    const TYPE_SETTING_VEHICLE = 3;
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
            [['id', 'last_num_contract', 'id_our_company', 'FLAG_EXPIRED_ORDER'], 'integer'],
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

    static public function getSetting($type = self::TYPE_SETTING){
        $setting = null;
        switch ($type){
            case self::TYPE_SETTING:
                $setting = Setting::find()->limit(1)->one();
                break;
            case self::TYPE_SETTING_VEHICLE:
                $setting = SettingVehicle::find()->limit(1)->one();
                break;
            case self::TYPE_SETTING_CLIENT:
                $setting = SettingClient::find()->limit(1)->one();
                break;
        }


    }
}
