<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "orders_finish_contacts".
 *
 * @property int $id_order
 * @property string $client_surname
 * @property string $client_name
 * @property string $client_phone
 * @property string $car_owner_surname
 * @property string $car_owner_name
 * @property string $car_owner_phone
 * @property string $driver_surname
 * @property string $driver_name
 * @property string $driver_phone
 * @property string $vehicle_brand
 * @property string $vehicle_number
 */
class OrdersFinishContacts extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders_finish_contacts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_order'], 'required'],
            [['id_order'], 'integer'],
            [['client_surname', 'client_name', 'client_phone', 'car_owner_surname',
                'car_owner_name', 'car_owner_phone', 'driver_surname', 'driver_name', 'driver_phone',
                'vehicle_brand', 'vehicle_number'], 'string', 'max' => 255],
            [['id_order'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_order' => 'Id Order',
            'client_surname' => 'Client Surname',
            'client_name' => 'Client Name',
            'client_phone' => 'Client Phone',
            'car_owner_surname' => 'Car Owner Surname',
            'car_owner_name' => 'Car Owner Name',
            'car_owner_phone' => 'Car Owner Phone',
            'driver_surname' => 'Driver Surname',
            'driver_name' => 'Driver Name',
            'driver_phone' => 'Driver Phone',
            'vehicle_brand' => 'Vehicle Brand',
            'vehicle_number' => 'Vehicle Number',
        ];
    }

    public function setData($client_surname, $client_name, $client_phone, $car_owner_surname, $car_owner_name, $car_owner_phone,
        $driver_surname, $driver_name, $driver_phone, $vehicle_brand, $vehacle_number){
        $this->client_surname = $client_surname;
        $this->client_name = $client_name;
        $this->client_phone = $client_phone;
        $this->car_owner_surname = $car_owner_surname;
        $this->car_owner_name = $car_owner_name;
        $this->car_owner_phone = $car_owner_phone;
        $this->driver_surname = $driver_surname;
        $this->driver_name = $driver_name;
        $this->driver_phone = $driver_phone;
        $this->vehicle_brand = $vehicle_brand;
        $this->vehicle_number = $vehacle_number;

    }
}
