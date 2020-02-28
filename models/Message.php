<?php

namespace app\models;

use app\components\functions\functions;
use Yii;
use app\components\DateBehaviors;
use yii\bootstrap\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "message".
 *
 * @property int $id
 * @property int $type
 * @property string $title
 * @property string $text
 * @property string $text_push
 * @property string $url
 * @property int $status
 * @property int $id_to_user
 * @property int $id_from_user
 * @property int $email_status
 * @property int $sms_status
 * @property int $push_status
 * @property int $create_at
 * @property boolean $can_review_client
 * @property boolean $can_review_vehicle
 * @property int $id_order
 * @property int $id_to_review
 * @property int $id_from_review
 */
class Message extends \yii\db\ActiveRecord
{
    const STATUS_NEED_TO_SEND = 1;
    const STATUS_SEND = 2;
    const STATUS_READ = 3;
    const STATUS_DELETE = 10;

    const TYPE_ALERT_CAR_OWNER_NEW_ORDER = 1;


    private $idPushall = "4781";

    private $keyPushall = "fbbc4ea3fbe1cdb2f7fc1b4246d48174";

    /**
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
            [['type', 'status', 'id_to_user', 'id_from_user', 'email_status',
                'sms_status', 'push_status', 'id_order'], 'integer'],
            [['id_to_user'], 'required'],
            [['title', 'url'], 'string', 'max' => 255],
            [['text', 'text_push'], 'string'],
            ['create_at', 'default', 'value' => date('d.m.Y H:i:s')],
            ['status','default', 'value' => self::STATUS_SEND],
            [[ 'can_review_client', 'can_review_vehicle', 'id_to_review', 'id_from_review'], 'safe']
        ];
    }

    public function behaviors()
    {
        return [
            'convertDateTime' => [
                'class' => 'app\components\DateBehaviors',
                'dateAttributes' => ['create_at'],
                'format' => DateBehaviors::FORMAT_DATETIME,
            ],
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
            'title' => 'Тема',
            'text' => 'Сообщение',
            'status' => 'Status',
            'id_to_user' => 'Id To User',
            'id_from_user' => 'Id From User',
            'email_status' => 'Email Status',
            'sms_status' => 'Sms Status',
            'push_status' => 'Push Status',
            'create_at' => 'Дата'
        ];
    }

    public function getReview(){
        return $this->hasOne(Review::class, ['id_message' => 'id']);
    }

    public function sendPush(bool $save = true)
    {
        if ($push_ids = User::findOne($this->id_to_user)->push_ids) {
            foreach ($push_ids as $push_id) {

                $url = Yii::$app->urlManager->createAbsoluteUrl([

                    'https://pushall.ru/api.php',

                    'type' => 'unicast',

                    'id' => $this->idPushall,

                    'key' => $this->keyPushall,

                    'uid' => $push_id,

                    'title' => $this->title,

                    'text' => $this->text_push,

                    'url' => $this->url,

                    'priority' => '1'

                ], 'https');

//                file_get_contents($url);

                curl_setopt_array($ch = curl_init(), array(
                    CURLOPT_URL => "https://pushall.ru/api.php",
                    CURLOPT_POSTFIELDS => array(
                        "type" => "unicast",
                        "id" => $this->idPushall,
                        "key" => $this->keyPushall,
                        "text" => $this->text_push,
                        "title" => 'perevozki40.ru ' . $this->title,
                        'priority' => '1',
                        'url' => $this->url,
                        'uid' => $push_id,
                    ),
                    CURLOPT_SAFE_UPLOAD => true,
                    CURLOPT_RETURNTRANSFER => true
                ));
                $return=curl_exec($ch); //получить данные о рассылке
                curl_close($ch);

                $this->push_status = self::STATUS_SEND;

//                return var_dump($this->getErrors());
            }
        } else {
            $this->push_status = self::STATUS_NEED_TO_SEND;
        }
//        if($save) $this->save();
    }

    public function  changeStatus($newStatus){
        $this->status = $newStatus;
        $this->save(false);
    }

    static public function countNewMessage($id_user){
        if(!$id_user) return null;
        $newMessages = Message::find()
            ->filterWhere(['id_to_user' => $id_user])
            ->orFilterWhere(['id_from_user' => $id_user])
            ->andWhere(['status' => self::STATUS_SEND])
            ->count();
        return ($newMessages)?$newMessages:null;
    }

    public function AlertNewOrder($id_user, $id_order){
        $id_user = null;
        $id_user = null;
        if($profile = Profile::findOne(['id_user' => $id_user])
            && $order = Order::findOne($id_order))
        {
            if($order->status == $order::STATUS_NEW
                || $order->status == $order::STATUS_IN_PROCCESSING) {
                $this->id_order = $id_order;
                $this->id_to_user = $id_user;
                $this->type = self::TYPE_ALERT_CAR_OWNER_NEW_ORDER;
                $this->title = 'НОВЫЙ ЗАКАЗ №' . $order->id;
                $this->url = Url::to('/order/vehicle');
                $this->text = '';
                $this->text_push = '';

                if ($profile->user->push_ids) {
                    $this->sendPush();
                }

                $email = [];
                if($profile->email) $email[] = $profile->email;
                if($profile->email2) $email[] = $profile->email2;
                functions::sendEmail([
                    $email, null,$this->title,
                    [
                        'html' => '@app/mail/views/order/new_order',
                        'text' => '@app/mail/views/empty_text',
                    ]
                ]);

            }
        }
        return false;
    }


}