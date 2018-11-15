<?php

namespace app\models;

use Yii;
use app\components\DateBehaviors;
use nickcv\encrypter\behaviors\EncryptionBehavior;
use yii\bootstrap\Html;

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
 * @property integer $brand_id
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


    public static function tableName()
    {
        return 'reg_licenses';
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
            [['number', 'place', 'country'], 'string', 'max' => 255],
            [['image1', 'image2'], 'image', 'extensions' => 'png, jpg, bmp', 'maxSize' => 2200000],
            ['reg_number', 'uniqueRegNumber', 'on' => 'create'],
            ['brand_id', 'required']

//            [['date'], 'date', 'format' => 'Php: d.m.Y']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID СР',
            'brand_id' => 'Марка ТС',
            'reg_number' => 'Гос. номер',
            'number' => 'Серия, номер свидетельства о регистрации:',
            'date' => 'Дата выдачи свидетельства о регистрации:',
            'place' => 'Кем выдано свидетельство о регистрации:',
            'country' => 'Страна выдачи свидетельства о регистрации:',
            'image1' => 'Скан или фото свидетельства о регистрации ТС (1 сторона)',
            'image2' => 'Скан или фото свидетельства о регистрации ТС (2 сторона)',
            'image1Html' => 'Скан или фото свидетельства о регистрации ТС (1 сторона)',
            'image2Html' => 'Скан или фото свидетельства о регистрации ТС (2 сторона)',
            'status' => 'Статус',
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
            ->where(['id_user' => Yii::$app->user->id])
            ->andWhere(['status' != Vehicle::STATUS_FULL_DELETED])
            ->all()
            ;
        if($vehicles) {
            foreach ($vehicles as $vehicle) {
                if ($this->reg_number == $vehicle->regLicense->reg_number) {
                    $this->addError($attribute, 'У Вас уже добавлено ТС с таким гос. номером.');
                }
            }
        }
    }

    public function deletePhoto(){
        $path1 = Yii::getAlias('@photo_reg_license/' . $this->image1);
        $path2 = Yii::getAlias('@photo_reg_license/' . $this->image2);
        if(file_exists($path1) && is_file($path1)) unlink($path1);
        if(file_exists($path2) && is_file($path2)) unlink($path2);
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
