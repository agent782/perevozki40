<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "message".
 *
 * @property int $id
 * @property int $type
 * @property string $title
 * @property string $text
 * @property int $status
 * @property int $user_id
 * @property int $email_status
 * @property int $sms_status
 * @property int $push_status
 */
class Message extends \yii\db\ActiveRecord
{
    const STATUS_SEND = 1;
    private $idPushall = "4781";
    private $keyPushall = "fbbc4ea3fbe1cdb2f7fc1b4246d48174";

    /*
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'message';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'status', 'user_id', 'email_status', 'sms_status', 'push_status'], 'integer'],
            [['user_id'], 'required'],
            [['title', 'text'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'title' => 'Title',
            'text' => 'Text',
            'status' => 'Status',
            'user_id' => 'User ID',
            'email_status' => 'Email Status',
            'sms_status' => 'Sms Status',
            'push_status' => 'Push Status',
        ];
    }

    public function sendPush(){
        $url = Yii::$app->urlManager->createAbsoluteUrl([
            '//https://pushall.ru/api.php',
            'type' => 'broadcast',
            'id' => $this->idPushall,
            'key' => $this->keyPushall,
            'title' => 'test2',
            'text' => 'TEST TEST',
            'url' => 'http://2.grigorov.org/order/vehicle',
            'priority' => '1'
        ], 'https');
        return file_get_contents($url);
    }
}
