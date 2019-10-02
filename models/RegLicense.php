<?php

namespace app\models;

use Yii;
use app\components\DateBehaviors;
use nickcv\encrypter\behaviors\EncryptionBehavior;
use yii\bootstrap\Html;
use app\models\setting\Setting;

/**
 * This is the model class for table "reg_licenses".
 *
 * @property integer $id
 * @property string $reg_number
 * @property string $number
 * @property integer $date
 * @property string $place
 * @property string $country
 * @property string $image1
 * @property string $image2
 * @property integer $status
 * @property string $brand
* @property string $image1Html
 * @property string $image2Html



 */
class RegLicense extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    const STATUS_UNSIGNED = 0;
    const STATUS_SIGNED = 1;
    const STATUS_ON_CHECKING = 2;
    const STATUS_FAILED = 3;
    public $image1File;
    public $image2File;

    public $id_user;

    public static function tableName()
    {
        return 'reg_licenses';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios(); // TODO: Change the autogenerated stub
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            [['number', 'reg_number', 'date', 'place'], 'required'],
            [['status'], 'default', 'value' => self::STATUS_ON_CHECKING],
            [['reg_number'], 'string', 'max' => 16],
            [['brand'], 'string', 'max' => 64],
            [['id_user'], 'safe'],
            [['number', 'place', 'country'], 'string', 'max' => 255],
            [['image1', 'image2'], 'image', 'extensions' => 'png, jpg, bmp', 'maxSize' => 5200000],
            ['reg_number', 'uniqueRegNumber'],
            ['brand', 'required'],
            ['date', 'date', 'format' => 'php: d.m.Y',
                'min' => (time() - 60*60*24*365*40),
                'max' => time(),
                'tooSmall' => 'Проверьте дату.',
                'tooBig' => 'Вы из будущего?)'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID СР',
            'reg_number' => 'Гос. номер',
            'number' => 'Серия, номер свидетельства о регистрации:',
            'date' => 'Дата выдачи свидетельства о регистрации:',
            'place' => 'Кем выдано свидетельство о регистрации:',
            'country' => 'Страна выдачи свидетельства о регистрации:',
            'image1' => 'Скан или фото свидетельства о регистрации ТС (1 сторона)',
            'image2' => 'Скан или фото свидетельства о регистрации ТС (2 сторона)',
            'image1File' => 'Скан или фото свидетельства о регистрации ТС (1 сторона)',
            'image2File' => 'Скан или фото свидетельства о регистрации ТС (2 сторона)',
            'image1Html' => 'Скан или фото свидетельства о регистрации ТС (1 сторона)',
            'image2Html' => 'Скан или фото свидетельства о регистрации ТС (2 сторона)',
            'status' => 'Статус',
            'brand' => 'Марка ТС (как в СТС, для пропусков  и т.п.)'
        ];
    }

    public function behaviors()
    {
        return [
            'encryption' => [
                'class' => '\nickcv\encrypter\behaviors\EncryptionBehavior',
                'attributes' => [
                    'reg_number',
                    'number',
//                    'image1',
//                    'image2'
                ],
            ],
            'convertDate' => [
                'class' => 'app\components\DateBehaviors',
                'dateAttributes' => ['date'],
                'format' => DateBehaviors::FORMAT_DATE,
            ]
        ];
    }
    public function uniqueRegNumber($attribute){

        $vehicles = Vehicle::find()
            ->where(['id_user' => $this->id_user])
            ->andWhere(['!=','status' , Vehicle::STATUS_FULL_DELETED])
            ->all()
            ;

        if($vehicles) {
            foreach ($vehicles as $vehicle) {
                $regLicense = $vehicle->regLicense;
                if($regLicense
                    && $this->id != $regLicense->id
                ) {
                    if (mb_ereg_replace(' ', '', $this->reg_number) == $regLicense->reg_number) {
                        $this->addError($attribute, 'У Вас уже добавлено ТС с таким гос. номером.');
                        break;
                    }
                }
            }
        }
    }

    public function beforeDelete()
    {
        $this->deletePhoto();
        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }

    public function deletePhoto(){
        $path1 = Yii::getAlias('@photo_reg_license/' . $this->image1);
        $path2 = Yii::getAlias('@photo_reg_license/' . $this->image2);
        if(file_exists($path1) && is_file($path1)) {
            if(!unlink($path1))return false;
        }
        if(file_exists($path2) && is_file($path2)) {
            if(!unlink($path2))return false;
        }
        $this->image1 = Setting::find()->one()->noPhotoPath;
        $this->image2 = Setting::find()->one()->noPhotoPath;
        return true;
    }

    public function getBrand(){
        return $this->hasOne(Brand::className(), ['id' => 'brand_id']);
    }

    public function getImage1Html(){
        return ($this->image1)?
            Html::a(Html::img(
                '/uploads/photos/reg_licenses/'.$this->image1, ['class' => 'profile_photo_min']),
                '/uploads/photos/reg_licenses/'.$this->image1, ['target' => 'blank']) :
            Html::img('/img/noPhoto.jpg', ['class' => 'profile_photo_min']);
    }
    public function getImage2Html(){
        return
            ($this->image2)?
                Html::a(Html::img(
                    '/uploads/photos/reg_licenses/'.$this->image2, ['class' => 'profile_photo_min']),
                    '/uploads/photos/reg_licenses/'.$this->image2, ['target' => 'blank']) :
                Html::img('/img/noPhoto.jpg', [ 'class' => 'profile_photo_min']);
    }

}
