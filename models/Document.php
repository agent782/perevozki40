<?php

namespace app\models;

use app\components\DateBehaviors;
use Dadata\Response\Date;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Breadlesscode\Office\Converter;
use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "documents".
 *
 * @property integer $id
 * @property integer $date
 * @property integer $type
 * @property string $url_download
 * @property string $url_upload
 * @property integer $checked
 * @property integer $id_company
 * @property integer $id_vehicle
 * @property integer $id_user
 * @property string $comments
 */
class Document extends \yii\db\ActiveRecord
{
    public $upload_file;
    public $confirm_file;


    const TYPE_CONTRACT_CLIENT = 1;


    const STATUS_UNSIGNED = 0;
    const STATUS_SIGNED = 1;
    const STATUS_ON_CHECKING = 2;
    const STATUS_FAILED = 3;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'documents';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'status', 'id_user'], 'integer'],
//            ['upload_file', 'required', 'message' => 'Выберите файл(ы)'],
            [['url_download', 'url_upload', 'comments'], 'safe'],
//            ['date', 'date', 'format' => 'php: d.m.Y'],
            ['date', 'default', 'value' => date('d.m.Y')],
            ['status', 'default', 'value' => 0],
            [['id_company', 'id_vehicle', 'url_confirm', 'companyName'], 'safe'],
            ['upload_file', 'file', 'maxFiles' => 10,  'checkExtensionByMimeType' => false, 'extensions' => 'pdf, jpg',
                'maxSize' => 4072000, 'tooBig' => 'Максимальный размер файлa 4MB']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'date' => Yii::t('app', 'Дата'),
            'type' => Yii::t('app', 'Тип'),
            'url_download' => Yii::t('app', 'Url Download'),
            'url_upload' => Yii::t('app', 'Url Upload'),
            'status' => Yii::t('app', 'Статус'),
            'id_company' => Yii::t('app', 'Id Company'),
            'id_vehicle' => Yii::t('app', 'Id Vehicle'),
            'id_user' => Yii::t('app', 'Id User'),
            'comments' => Yii::t('app', 'Комментарии'),
            'companyName' => Yii::t('app', 'Название организации или ИП'),
        ];
    }

    public function behaviors()
    {
       return[
           'dateConvert' => [
               'class' => DateBehaviors::className(),
               'dateAttributes' => ['date'],
               'format' => DateBehaviors::FORMAT_DATE,
           ]
       ];
    }

    public function create(){

    }

    public function updateDoc($id, $type){

    }
    // Сохранение договора клиента при регистрации организации
    public function saveContractClient($id_company, $type){
//        $this->type = self::TYPE_CONTRACT_CLIENT;
        $this->type = $type;
        $this->id_company = $id_company;
        $this->id_user = Yii::$app->user->getId();
        if($this->save()){
            return $this;
        }
        return false;
    }
    //создается договор для организации и возвращается $this
    public function createPdfContractClient($idCompany){
        $modelCompany = Company::findOne(['id' => $idCompany]);
        $phpword =  new PhpWord();

        $doc = $phpword->loadTemplate('documents/templates/client_contract.docx');

        $doc->setValue('id', $this->id);
        $doc->setValue('date', $this->date);
        $doc->setValue('company_name', $modelCompany->name);
        $doc->setValue('FIO_contract', $modelCompany->FIO_contract);
        $doc->setValue('basis_contract', $modelCompany->basis_contract);
        $doc->setValue('job_contract', $modelCompany->job_contract);
        $doc->setValue('address', $modelCompany->address);
        $doc->setValue('address_post', $modelCompany->address_post);
        $doc->setValue('inn', $modelCompany->inn);
        $doc->setValue('kpp', $modelCompany->kpp);
        $doc->setValue('phones', $modelCompany->phone);
        $doc->setValue('emails', $modelCompany->email);

//        $tmpPathDocx = 'tmp/tmp' . $this->id .'.docx';
        $tmpPathDocx = Yii::getAlias('@tmp/tmp' . $this->id .'.docx');
        $doc->saveAs($tmpPathDocx);
//
        $this->url_download = $this->id .'.pdf';
        Converter::file($tmpPathDocx) // Раскомментировать для удаленки! select a file for convertion
          ->setLibreofficeBinaryPath('/usr/bin/libreoffice') // Раскомментировать для удаленки! binary to the libreoffice binary
          ->setTemporaryPath(Yii::getAlias('@tmp')) // temporary directory for convertion
//          ->setTimeout(100) // libreoffice process timeout
        ->save(Yii::getAlias('@client_contracts_forms/' .$this->url_download)); // save as pdf //Раскомментировать для удаленки!
        return $this;
    }



    public function getStatusString(){
        switch ($this->status) {
            case self::STATUS_UNSIGNED: return 'НЕ ПОДПИСАН';
            case self::STATUS_SIGNED: return 'ПОДПИСАН';
            case self::STATUS_ON_CHECKING: return 'НА ПРОВЕРКЕ';
            case self::STATUS_FAILED: return 'НЕ ПРОШЕЛ ПРОВЕРКУ';
            default: return '';
        }
    }

    public function getTypeString(){
        switch ($this->type){
            case self::TYPE_CONTRACT_CLIENT:
                return 'Договор с клиентом';
        }
    }

    public static function getTypies(){
        $typies = [];
        $typies [1] = 'Договор с клиентом';
        $typies [2] = 'Тестовая хапись';
        return $typies;
    }

    public static function getStatuses(){
        $statuses = [];
        $statuses [0] = 'Не подписан';
        $statuses [1] = 'Подписан';
        $statuses [2] = 'На проверке';
        $statuses [3] = 'Не прошел проверку';

        return $statuses;
    }

    public function getCompany(){
        return $this->hasOne(Company::className(), ['id' => 'id_company']);
    }

    public function getCompanyName(){
        return $this->company->name;
    }

    public function getUrlsUpload(){
        $urlsArray = unserialize($this->url_upload);
        if(is_array($urlsArray)){
            $urlsHtml = '';
            foreach ($urlsArray as $url){
                $urlsHtml .= Html::a(
                    $url,
                    \yii\helpers\Url::to(['/document/download-doc-on-check', 'url' => $url, 'type' => $this->type])
                    )
                    . '<br>';
            }
            return $urlsHtml;
        }
        return false;
    }
}
