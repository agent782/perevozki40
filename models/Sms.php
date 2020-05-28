<?php

namespace app\models;

use app\components\functions\functions;
use Yii;
use app\components\DateBehaviors;
/**
 * This is the model class for table "sms".
 *
 * @property string $id
 * @property string $to
 * @property string $from
 * @property string $message
 * @property integer $status
 * @property string $status_text
 * @property double $cost
 * @property integer $date
 */
class Sms extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sms';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['cost'], 'number'],
            [['id', 'status_text'], 'string', 'max' => 255],
            [['to', 'from'], 'string', 'max' => 15],
            [['message'], 'string', 'max' => 128],
            ['date', 'default', 'value' => date('d.m.Y h:i')]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'to' => 'To',
            'from' => 'From',
            'message' => 'Message',
            'status' => 'Status',
            'cost' => 'Cost',
            'date' => 'Date',
        ];
    }

    public function behaviors()
    {
        return [
            'convertDate' => [
                'class' => DateBehaviors::className(),
                'dateAttributes' => [
                    'date'
                ],
                'format' => DateBehaviors::FORMAT_DATETIME,
            ]
        ];
    }

    public function __construct($to = null, $message = null, $from = null)
    {
        $this->to = $to;
        $this->message = $message;
        $this->from = Yii::$app->params['smsFrom'];
        $this->cost = $this->setCost();
    }

   static public function findById($id){
        return Sms::findOne(['id' => $id]);
    }

    public function sendAndSave(){
        if(!$this->to || !$this->message) return false;

        foreach (Yii::$app->smsru->send($this->to, $this->message)->sms as $smsStatus) {
            $this->status = $smsStatus->status_code;
            if($smsStatus->sms_id) {
                $this->id = $smsStatus->sms_id;
            }else {
                $this->id = 'error_' . time() . '_' . rand(10000, 99999);
            }
            $this->status_text = $smsStatus->status;
            if ($this->save()) return $this;
//            return $this->getErrors();
        }
        return false;
    }

    public function setId(){

    }

    public function getId(){

    }

    public function setCost(){
        $cost = 0;

        foreach (Yii::$app->smsru->cost($this->to, $this->message)->sms as $sms){
            if($sms->status == 100 || $sms->status == 101 || $sms->status == 102) {
                return $this->cost = $sms->cost;
            }
        }
        return null;
    }

    static public function checkBalance(){
        $balance = Yii::$app->smsru->balance()->balance;

        if($balance>992&&$balance<1000 || $balance>492&&$balance<500){
            functions::sendEmail(
                [Yii::$app->params['adminEmail'],Yii::$app->params['financeEmail']],
                null,
                'СМС баланс',
                ['balance' => $balance],
                [
                    'html' => 'views/endSmsBalance',
                    'text' => 'views/endSmsBalance',
                ],
                null
            );
        }
        return $balance;
    }

    static public function updateStatuses(){
        $smses = Sms::find()
            ->where(['>', 'date', (time()-(3600*24*3))])
            ->all();
        $updates = 0;
        foreach ($smses as $sms) {
            if($sms->status == 100 || $sms->status == 101 || $sms->status == 102) {
                if (Yii::$app->smsru->status($sms->id)->status_code > 0) {
                    foreach (Yii::$app->smsru->status($sms->id)->sms as $smsStatus) {
                        $sms->status = $smsStatus->status_code;
                        $sms->status_text = $smsStatus->status_text;
                        $sms->cost = $smsStatus->cost;
                        $sms->update();

                        $updates++;
                    }
                }
            }
        }
        return $updates;
    }



}
