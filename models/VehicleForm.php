<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 29.05.2018
 * Time: 11:33
 */

namespace  app\models;


use app\components\functions\functions;
use yii\base\Model;
use Yii;
use yii\helpers\ArrayHelper;

class VehicleForm extends Model
{

    const TYPE_TRUCK = 1;
    const TYPE_PASSENGER = 2;
    const TYPE_SPEC = 3;

    public $id;
    public $id_user;
    public $tonnage;
    public $length;
    public $width;
    public $height;
    public $volume;
    public $longlength;
    public $passengers;
    public $ep;
    public $rp;
    public $lp;
    public $tonnage_spec;
    public $length_spec;
    public $volume_spec;
    public $description;

    public $vehicleTypeId;
    public $bodyTypeId;
    public $loadingTypeIds = [];
    public $photo;
    public $price_zones = [];
//    public $ClassiferVehicleIds = [];

    public $instruction_to_driver;
    public $confidentiality_agreement;
    public $use_conditions;

    public function rules()
    {
        return [
            [['price_zones']
                , 'required', 'message' => 'Выберите хотя бы один из вариантов.
                 Если вариантов нет, проверьте корректность введенных данных по машине или обратитесь к администратору.'],
            [[
                'id_user',
                'vehicleTypeId',
                'bodyTypeId',
                'loadingTypeIds',
            ]
                , 'required', 'message' => 'Выберите хотя бы один из вариантов.'],
            [[
                'tonnage',
                'length',
                'height',
                'width',
                'volume',
                'length_spec',
                'tonnage_spec',
                'volume_spec',
                'passengers',
            ], 'number'],
            ['tonnage', 'validateTonnage', 'skipOnEmpty' => false, 'enableClientValidation' => true],
            [['length', 'width'], 'validateLengthWidth', 'skipOnEmpty' => false, 'enableClientValidation' => true],
            [['height'], 'validateHeigth', 'skipOnEmpty' => false, 'enableClientValidation' => true],
            ['volume', 'validateVolume', 'skipOnEmpty' => false, 'enableClientValidation' => true],
            [['tonnage_spec', 'length_spec'], 'validateSpecTonnageLength', 'skipOnEmpty' => false, 'enableClientValidation' => true],
            [['volume_spec'], 'validateSpecVolume', 'skipOnEmpty' => false, 'enableClientValidation' => true],
            ['passengers', 'validatePassengers', 'skipOnEmpty' => false, 'enableClientValidation' => true],
            [[
                'id',
                'longlength',
                'description',
                'lp',
                'rp',
                'ep',
                'price_zones'
            ], 'safe'],
            ['photo', 'image', 'extensions' => 'png, jpg, bmp', 'maxSize' => 4000000],
            [['instruction_to_driver', 'confidentiality_agreement', 'use_conditions'],
                'compare', 'compareValue' => 1, 'operator' => '==', 'skipOnEmpty' => false, 'skipOnError' => false,
                'message' => 'Подтвердите согласие.'],

        ];
    }
    public function validateTonnage($attribute){
        if(!$this->$attribute &&
            ($this->bodyTypeId != Vehicle::BODY_crane
                && $this->bodyTypeId != Vehicle::BODY_excavator
                && $this->bodyTypeId != Vehicle::BODY_excavator_loader )){
            $this->addError($attribute, 'Необходимо заполнить "' . $this->getAttributeLabel($attribute) . '"');
        }
    }
    public function validateLengthWidth($attribute){
        if(!$this->$attribute && $this->vehicleTypeId != Vehicle::TYPE_PASSENGER &&
            ($this->bodyTypeId != Vehicle::BODY_crane
                && $this->bodyTypeId != Vehicle::BODY_excavator
                && $this->bodyTypeId != Vehicle::BODY_excavator_loader
                && $this->bodyTypeId != Vehicle::BODY_dump
            )){
            $this->addError($attribute, 'Необходимо заполнить "' . $this->getAttributeLabel($attribute) . '"');
        }
    }

    public function validateHeigth($attribute){
        if(!$this->$attribute && $this->vehicleTypeId == Vehicle::TYPE_TRUCK){
            $this->addError($attribute, 'Необходимо заполнить "' . $this->getAttributeLabel($attribute) . '"');
        }
    }

    public function validateVolume($attribute){
        if(!$this->$attribute &&
            ($this->bodyTypeId != Vehicle::BODY_crane
                && $this->bodyTypeId != Vehicle::BODY_excavator
                && $this->bodyTypeId != Vehicle::BODY_excavator_loader
                && $this->bodyTypeId != Vehicle::BODY_manipulator)){
            $this->addError($attribute, 'Необходимо заполнить "' . $this->getAttributeLabel($attribute) . '"');
        }
    }

    public function validateSpecTonnageLength($attribute){
        if(!$this->$attribute &&
            ($this->bodyTypeId == Vehicle::BODY_crane
            || $this->bodyTypeId == Vehicle::BODY_manipulator))
        {
            $this->addError($attribute, 'Необходимо заполнить "' . $this->getAttributeLabel($attribute) . '"');
        }
    }

    public function validateSpecVolume($attribute){
        if(!$this->$attribute && (
            $this->bodyTypeId == Vehicle::BODY_excavator_loader || $this->bodyTypeId == Vehicle::BODY_excavator)) {
            $this->addError($attribute, 'Необходимо заполнить "' . $this->getAttributeLabel($attribute) . '"');
        }
    }

    public function validatePassengers($attribute){
        if(!$this->$attribute && $this->vehicleTypeId == Vehicle::TYPE_PASSENGER){
            $this->addError($attribute, 'Необходимо заполнить "' . $this->getAttributeLabel($attribute) . '"');
        }
    }

    public function validateLonglength($attribute){
        if(!$this->$attribute){
            $this->addError($attribute, 'Необходимо заполнить.');
        }
    }

    public function attributeLabels()
    {
        return [
            'tonnage' => 'Максимальная грузоподъемность (тонн):',
            'length' => 'Длина кузова (метры):',
            'width' => 'Ширина кузова (метры):',
            'height' => 'Высота кузова (метры):',
            'volume' => 'Объем кузова(м3):',
            'longlength' => 'Перевозка "длинномера":',
            'passengers' => 'Количество пассажиров:',
            'ep' => 'Количество "европоддонов" 0,8*1,2м:',
            'rp' => 'Количество "русских" поддонов 1,0*1,2м:',
            'lp' => 'Количество поддонов 1,2*1,2м:',
            'tonnage_spec' => 'Грузоподъемность механизма (стрелы) (тонны):',
            'length_spec' => 'Длина механизма (стрелы) (метры):',
            'volume_spec' => 'Объем механизма (ковша) (м3):',
            'description' => 'Комментарии:',
            'photos' => 'Фотографии',
            'vehicleTypeId' => 'Тип транспорта:',
            'bodyTypeId' => 'Тип кузова:',
            'loadingTypeIds' => 'Тип погрузки/выгрузки:',
            'photo' => 'Фотография авто:',
            'description' => 'Дополнительная информация (Видна зарегистрированным клиентам):',
            'price_xones' => 'Тарифные зоны:'

        ];
    }

    public function getPriceZones(){
        $result = [];
        $priceZones = PriceZone::find()->where(['status' => PriceZone::STATUS_ACTIVE]);
        switch ($this->vehicleTypeId){
            case Vehicle::TYPE_TRUCK:
                $priceZones = $priceZones
                    ->andFilterWhere(['<=', 'tonnage_max', $this->tonnage])
                    ->orFilterWhere(
                        ['<=', 'tonnage_min', $this->tonnage]
                        )
                    ->andFilterWhere(['<=', 'volume_min', $this->volume])
                    ->orFilterWhere(
                        ['<=', 'volume_max', $this->volume]
                    )
                    ->andFilterWhere(['<=', 'length_min', $this->length])
                    ->orFilterWhere(
                        ['<=', 'length_max', $this->length]
                    )
//                    ->all()
                ;
//                break;
            case Vehicle::TYPE_PASSENGER:

                break;
            case Vehicle::TYPE_SPEC:

                break;
        }

        (!$this->longlength)
            ? $priceZones = $priceZones->andFilterWhere(['longlength' => $this->longlength])->orderBy(['r_km'=>SORT_DESC, 'r_h'=>SORT_DESC])->all()
            : $priceZones = $priceZones->orderBy(['r_km'=>SORT_DESC, 'r_h'=>SORT_DESC])->all();
//        $priceZones = $priceZones->all();
        foreach ($priceZones as $priceZone){
            if($priceZone->hasBodyType($this->bodyTypeId))
                $result[$priceZone->id] = 'Тарифная зона ' . $priceZone->id;
        }
        return $result;
    }

    public function saveVehicle($reg_licence_ID, $id_user){
        if(!$this->id){
            $modelVehicle = new Vehicle(['scenario' => Vehicle::SCENARIO_CREATE]);
            $modelVehicle->id_user = Yii::$app->user->id;
            $modelVehicle->id_user = $id_user;
            if(!$modelVehicle->id_user) return false;
            $modelVehicle->create_at = date('d.m.Y');
        }
        else {
            $modelVehicle = Vehicle::find()->where(['id' => $this->id])->one();
            $modelVehicle->update_at = date('d.m.Y');
        }
//        return var_dump($modelVehicle);
        $modelVehicle->status = Vehicle::STATUS_ONCHECKING;
        $modelVehicle->id_vehicle_type = $this->vehicleTypeId;
        $modelVehicle->body_type = $this->bodyTypeId;
        $modelVehicle->tonnage = $this->tonnage;
        $modelVehicle->length = $this->length;
        $modelVehicle->width = $this->width;
        $modelVehicle->height = $this->height;
        $modelVehicle->volume = $this->volume;
        $modelVehicle->longlength = $this->longlength;
        $modelVehicle->passengers = $this->passengers;
        $modelVehicle->ep = $this->ep;
        $modelVehicle->lp = $this->lp;
        $modelVehicle->rp = $this->rp;
        $modelVehicle->tonnage_spec = $this->tonnage_spec;
        $modelVehicle->length_spec = $this->length_spec;
        $modelVehicle->volume_spec = $this->volume_spec;
        $modelVehicle->description = $this->description;
        $modelVehicle->reg_license_id = $reg_licence_ID;

        if($modelVehicle->save()){
            foreach ($this->loadingTypeIds as $loadingTypeId){
                $loadingType = LoadingType::find()->where(['id' => $loadingTypeId])->one();
                $modelVehicle->link('loadingtypes', $loadingType);
            }
            $price_zones = $this->price_zones;
            if($price_zones) {
                foreach ($price_zones as $price_zone) {
                    $PriceZone = PriceZone::find()->where(['id' => $price_zone])->one();
                    if ($PriceZone) $modelVehicle->link('price_zones', $PriceZone);
                }
            }
            $modelVehicle->photo = functions::saveImage($this, 'photo', Yii::getAlias('@photo_vehicle/'), $modelVehicle->id);

            if($modelVehicle->save()) {
                Yii::$app->session->setFlash('success', 'Успешно сохранено.' );
                return $modelVehicle;
            }
            Yii::$app->session->setFlash('warning', 'ОШИБКА. Попробуйте еще раз.');
            return false;
        }
return var_dump($modelVehicle->getErrors());
        return false;
    }


}

