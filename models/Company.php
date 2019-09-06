<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use app\components\DateBehaviors;
use app\components\functions\functions;
/**
 * This is the model class for table "company".
 *
 * @property integer $id
 * @property string $inn
 * @property integer $id_address
 * @property integer $id_address_real
 * @property integer $id_address_post
 * @property string $value
 * @property string $address-value
 * @property string $branch_type
 * @property string $capital
 * @property string $email
 * @property string $email2
 * @property string $email3
 * @property integer $kpp
 * @property string $management_name
 * @property string $management_post
 * @property string $name-full
 * @property string $name_short
 * @property string $ogrn
 * @property integer $ogrn_date
 * @property string $okpo
 * @property string $okved
 * @property string $opf-short
 * @property string $phone
 * @property string $phone2
 * @property string $phone3
 * @property string $citizenship
 * @property integer $state_actuality_date
 * @property integer $state_registration_date
 * @property integer $state_liquidation_date
 * @property string $state-status
 * @property integer $data-type
 * @property integer $status
 * @property integer $raiting
 * @property integer $created_at
 * @property integer $updated_at
 * @property array $balance
 */


class Company extends \yii\db\ActiveRecord
{

    const STATUS_NEW = 0;
    const STATUS_WAIT = 1;
    const STATUS_CHECKED = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'company';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['inn', 'name', 'address', 'email',
                'ogrn', 'ogrn_date', 'phone', 'FIO_contract', 'basis_contract', 'job_contract'], 'required'],
//            [['kpp', 'status', 'raiting'], 'integer'],
//            [['id_address', 'id_address_real', 'id_address_post', 'inn', 'value', 'address_value', 'branch_type', 'email', 'email2', 'email3',
//                'management_name', 'management_post', 'name_full', 'name_short',
//                'phone', 'phone2', 'phone3', 'citizenship', 'state_status'], 'safe'],
//            [['opf_short'], 'string', 'max' => 64],
//            [['phone','phone2','phone3'], 'string', 'length'=> [10,10], 'tooLong'=> 'Неверный формат номера "9105234777"', 'tooShort'=> 'Неверный формат номера "9105234777"'],
            [['email', 'email2', 'email3'], 'email'],
//            [['inn'], 'unique'],
//            [['ogrn_date'], 'required'],
            [['ogrn_date'], 'date', 'format' => 'php:d.m.Y',
                'skipOnError' => false,
                'skipOnEmpty' => false,
                'min' => (time() - 60*60*24*365*50),
                'max' => time(),
                'tooSmall' => 'Проверьте дату.',
                'tooBig' => 'Вы из будущего?)'
            ],
            [[
                    'name',
                    'address_real',
                    'address_post',
                    'value',
                    'address_value',
                    'branch_type',
                    'capital',
                    'email',
                    'kpp',
                    'management_name',
                    'management_post',
                    'name_full',
                    'name_short',
                    'okpo',
                    'okved',
                    'opf_short',
                    'citizenship',
                    'state_actuality_date',
                    'state_registration_date',
                    'state_liquidation_date',
                    'state_status',
                    'data_type',
                    'status',
                    'raiting','phone2','phone3'
                ], 'safe'],
            ['status', 'default', 'value' => self::STATUS_NEW],
            ['raiting', 'default', 'value' => 0],
            [['created_at', 'updated_at'], 'default', 'value' => date('d.m.Y')]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'id'),
            'inn' => Yii::t('app', 'ИНН'),
            'name' => Yii::t('app', 'Наименование организации или ИП'),
            'address' => Yii::t('app', 'Юридический адрес'),
            'address_real' => Yii::t('app', 'Фактический адрес'),
            'address_post' => Yii::t('app', 'Почтовый адрес'),
            'value' => Yii::t('app', 'ФИАС Наименование организации.'),
            'address_value' => Yii::t('app', 'ФИАС Адрес организации одной строкой'),
            'branch_type' => Yii::t('app', 'ФИАС Тип подразделения MAIN — головная организация; BRANCH — филиал.'),
            'capital' => Yii::t('app', 'ФИАС Уставной капитал (только для организаций)'),
            'email' => Yii::t('app', 'Эл. почта'),
            'email2' => Yii::t('app', 'Дополнительная эл. почта'),
            'email3' => Yii::t('app', 'Дополнительная эл. почта №2'),
            'kpp' => Yii::t('app', 'КПП'),
            'management_name' => Yii::t('app', 'ФИО руководителя'),
            'management_post' => Yii::t('app', 'Должность руководителя'),
            'name_full' => Yii::t('app', 'ФИАС Полное наименование'),
            'name_short' => Yii::t('app', 'ФИАС Сокращенное наименование'),
            'ogrn' => Yii::t('app', 'ОГРН'),
            'ogrn_date' => Yii::t('app', 'Дата выдачи ОГРН'),
            'okpo' => Yii::t('app', 'ОКПО'),
            'okved' => Yii::t('app', 'ОКВЭД'),
            'opf_short' => Yii::t('app', 'ОПФ'),
            'phone' => Yii::t('app', 'Телефон организации'),
            'phone2' => Yii::t('app', 'Дополнительный телефон'),
            'phone3' => Yii::t('app', 'Дополнительный телефон №2'),
            'citizenship' => Yii::t('app', 'Гражданство (только для ИП)'),
            'state_actuality_date' => Yii::t('app', 'Дата актуальности сведений о компании'),
            'state_registration_date' => Yii::t('app', 'Дата регистрации'),
            'state_liquidation_date' => Yii::t('app', 'Дата ликвидации'),
            'state_status' => Yii::t('app', 'Статус организации ACTIVE — действующая; LIQUIDATING — ликвидируется; LIQUIDATED — ликвидирована.'),
            'data_type' => Yii::t('app', 'Тип организации LEGAL — юридическое лицо; INDIVIDUAL — индивидуальный предприниматель.'),
            'status' => Yii::t('app', 'Статус'),
            'raiting' => Yii::t('app', 'Рейтинг'),
            'created_at' => Yii::t('app', 'Дата создания'),
            'updated_at' => Yii::t('app', 'Дата редактирования'),
            'FIO_contract' => Yii::t('app', 'ФИО подпиcывающего договор'),
            'basis_contract' => Yii::t('app', 'Действующий на основании'),
            'job_contract' => Yii::t('app', 'Должность подписывающего договор'),
        ];
    }

    public function behaviors()
    {
        return [
            DateBehaviors::class,
            'convertDate' => [
                'class' => DateBehaviors::className(),
                'dateAttributes' => ['ogrn_date', 'created_at', 'updated_at'],
                'format' => DateBehaviors::FORMAT_DATE,
            ],
//            'encryption' => [
//                'class' => '\nickcv\encrypter\behaviors\EncryptionBehavior',
//                'attributes' => [
//
//                ],
//            ],
        ];
    }

    public function getProfiles()
    {
        return $this->hasMany(Profile::className(), ['id_user' => 'id_profile'])
            ->viaTable('XprofileXcompany',['id_company' => 'id']);
    }
// для текущего пользователя
    public function getXprofileXcompany($idUser){
        return $this->hasOne(XprofileXcompany::className(), ['id_company' => 'id', 'id_profile' => $idUser]);
    }

    public function getXprofXcom($id_user){
        return XprofileXcompany::findOne(['id_company' => $this->id, 'id_profile' => $id_user]);
    }
    // для всех пользователя
    public function getXAllProfilesXcompany(){
        return $this->hasMany(XprofileXcompany::className(), ['id_company' => 'id',]);
    }

    public function getOrders(){
        return $this->hasMany(Order::class, ['id_company' => 'id']);
    }

    public function getPayments(){
        return $this->hasMany(Payment::class, ['id_company' => 'id']);
    }
    // Компания проверена хотя бы для одного пользователя
    public function checked($id_company){
        $company = Company::findOne($id_company);
        $profiles = $company->xAllProfilesXcompany;
        foreach ($profiles as $profile){
            if($profile->checked) return true;
        }
        return false;
    }

    //юр лицо в списке текущего пользователя
    static public function CompanyBelongsUser($user_id, $company_id){
        return XprofileXcompany::find()
            ->where([
                'id_company' => $company_id,
            ])
            ->andWhere([
                'id_profile' => $user_id
            ])
            ->one();

    }
    //число привязашшых профилей
    public function countProfiles($id_company){
        return XprofileXcompany::find()->where(['id_company' => $id_company])->count();
    }


    public function createDocument ($type){
        $document = Document::findOne(['id_company' => $this->id, 'type' => $type]);
        if(!$document) {
            $document = new Document();
            $document->type = $type;
            $document->id_company = $this->id;
            $document->id_user = Yii::$app->user->getId();
            if(!$document->save()) return false;
        }
        switch ($type) {
            case Document::TYPE_CONTRACT_CLIENT:
                $document = $document->createPdfContractClient();
        }
        if ($document->save()) {
            return $document;
        }
        return false;
    }

    public function getConfirmDoc(){
        return Document::findOne(['id_company' => $this->id, 'type' => Document::TYPE_CONTRACT_CLIENT]);
    }

    public function getCompanyInfo($showPhone = true,  $showEmail = true){
        $return = $this->name . '<br>';
        if($showPhone) {
            $return .=  functions::getHtmlLinkToPhone($this->phone);
            if ($this->phone2) $return .= ' (доп. ' . functions::getHtmlLinkToPhone($this->phone2) . ')';
            $return .= '. <br>';
        }
        if($showEmail) $return .= 'Email: ' . $this->email . ' (' . $this->email2 . ') <br>';

        return $return;
    }

    static public function getArrayForAutoComplete(bool $forSearch = false){
        $return = [];

        foreach (self::find()->all() as $company){
            $return[] = [
                'label' => $company->name . '(' . $company->inn . ')',
                'value' =>
                    ($forSearch)
                    ? $company->inn
                    : $company->name . '(' . $company->inn . ')'
                ,
                'id' => $company->id
            ];
        }

        return $return;
    }

    public function getBalance(){
        $return = [
            'balance' => 0,
            'orders' => []
        ];
        foreach ($this->orders as $order) {

            if($order->type_payment != Payment::TYPE_CASH) {
                $return['balance'] -= $order->cost_finish;
//            $return[$this->id]['balance'] -= $order->cost_finish;
                $return['orders'][] = [
                    'date' => $order->datetime_finish,
                    'credit' => $order->cost_finish,
                    'description' => 'Заказ № ' . $order->id,
                    'id_order' => $order->id,
                ];
            }else {
                $return['orders'][] = [
                    'date' => $payment->date,
                    'debit' => $payment->cost,
                    'credit' => $payment->cost,
                    'description' => $payment->comments . ' Оплачен наличными водителю.',
                    'id_paiment' => $payment->id
                ];
            }
        }
        foreach ($this->payments as $payment) {
                $return['balance'] += $payment->cost;
                $return['orders'][] = [
                    'date' => $payment->date,
                    'debit' => $payment->cost,
                    'description' => $payment->comments,
                    'id_paiment' => $payment->id
                ];
        }
        array_multisort($return['orders'], SORT_DESC);
        return $return;
    }

}