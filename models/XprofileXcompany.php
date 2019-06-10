<?php

namespace app\models;

use Yii;
use PhpOffice\PhpWord\PhpWord;
use Breadlesscode\Office\Converter;
use app\components\DateBehaviors;
use yii\bootstrap\Html;
use PhpOffice\PhpWord\Shared\ZipArchive;



/**
 * This is the model class for table "XprofileXcompany".
 *
 * @property integer $id
 * @property integer $id_profile
 * @property integer $id_company
 * @property string $job_post
 * @property string $url_poa
 * @property string $url_upload_poa
 * @property integer $term_of_office
 * @property integer $checked
 * @property string $url_form
 * @property string $STATUS_POA
 * @property string $comments
 * @property string $date
 * @property string $sTATUS_OF_ATTORNEY
 * @property string $companyName
 * @property string $profile
 * @property string $urlsUpload
 * @property string $fio

 */
class XprofileXcompany extends \yii\db\ActiveRecord
{
//Статусы доверенности

    const ClientPOA = 'ClientPOA';

    const STATUS_POWER_OF_ATTORNEY_UNSIGNED = 0;
    const STATUS_POWER_OF_ATTORNEY_SIGNED = 1;
    const STATUS_POWER_OF_ATTORNEY_ON_CHECKING = 2;
    const STATUS_POWER_OF_ATTORNEY_FAILED = 3;


    public $upload_file;
    public $idPOA;
    public $date;
    /**
     * @inheritdoc
     */

    public static function tableName()
    {
        return 'XprofileXcompany';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_profile', 'id_company'], 'required'],
//            ['upload_file', 'required', 'message' => 'Выберите файл(ы)'],
            [['id_profile', 'id_company'], 'integer'],
            [['job_post'], 'string', 'max' => 255],
            ['id', 'string'],
            [['idPOA','checked', 'STATUS_POA', 'url_poa', 'url_upload_poa', 'url_form', 'comments'], 'safe'],
            ['upload_file', 'file', 'maxFiles' => 1,  'checkExtensionByMimeType' => false, 'extensions' => 'pdf, jpg',
                'maxSize' => 4072000, 'tooBig' => 'Максимальный размер файлa 4MB'],
            ['STATUS_POA', 'default', 'value' => self::STATUS_POWER_OF_ATTORNEY_UNSIGNED],
            [['term_of_office', 'date'] , 'date', 'format' => 'php:d.m.Y', 'message' => 'Неверный формат даты. Введите "дд.мм.гггг". Наприсер 21.10.2020.'],

//            ['term_of_office' , 'date']

        ];
    }

    public function behaviors()
    {
        return [
            'convertDate' => [
                'class' => 'app\components\DateBehaviors',
                'dateAttributes' => ['term_of_office'],
                'format' => DateBehaviors::FORMAT_DATE,
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Номер доверенности'),
            'id_profile' => Yii::t('app', 'Id Profile'),
            'id_company' => Yii::t('app', 'Id Company'),
            'companyName' => Yii::t('app', 'Наименование организации или ИП'),
            'job_post' => Yii::t('app', 'Должность'),
            'url_form' => Yii::t('app', 'Форма доверенности'),
            'url_upload_poa' => Yii::t('app', 'Доверенность на проверку'),
            'url_poa' => Yii::t('app', 'Доверенность'),
            'term_of_office' => Yii::t('app', 'Срок доверенности до'),
            'checked' => Yii::t('app', 'Проверено'),
            'STATUS_POA' => Yii::t('app', 'Статус'),
            'comments' => Yii::t('app', 'Комментарии'),
            'sTATUS_POWER_OF_ATTORNEY' => Yii::t('app', 'Доверенность'),
            'fio' => Yii::t('app', 'ФИО'),
        ];
    }

    public function getSTATUS_POWER_OF_ATTORNEY(){
        switch ($this->STATUS_POA){
            case self::STATUS_POWER_OF_ATTORNEY_UNSIGNED:
                return 'Не подписана';
            case self::STATUS_POWER_OF_ATTORNEY_SIGNED:
                return 'Проверена';
            case self::STATUS_POWER_OF_ATTORNEY_ON_CHECKING:
                return 'На проверке';
            case self::STATUS_POWER_OF_ATTORNEY_FAILED:
                return 'Не прошла проверку или просрочена';
        }
    }
public function getCompany(){
    return $this->hasOne(Company::className(), ['id' => 'id_company'])
//        ->andWhere(['id_profile' => Yii::$app->user->id]);
    ;
    }
public function getProfile(){
    return $this->hasOne(Profile::className(), ['id_user'=>'id_profile']);
}

public function createPowerOfAttorneyForm(){
    $phpword =  new PhpWord();
    $modelCompany = $this->company;

    if(!$modelCompany) {
        Yii::$app->session->setFlash('warning', 'Должны быть заполнены все реквизиты юр.лица');
        return false;
    }
    $modelProfile = $this->profile;
    if(!$modelProfile) {
        Yii::$app->session->setFlash('warning', 'Заполните все поля Профиля.');
        return false;
    }
    $modelPassport = $modelProfile->passport;
    if(!$modelPassport) {
        Yii::$app->session->setFlash('warning', 'Заполните паспортные данные в разделе Профиль.');
        return false;
    }

    $doc = $phpword->loadTemplate('documents/templates/power_of_attorney.docx');

    $doc->setValue('id', $this->id_profile.'-'.$this->id_company );
    $doc->setValue('date', date('d.m.Y'));
    $doc->setValue('company_name', $modelCompany->name);
    $doc->setValue('company_address', $modelCompany->address);
    $doc->setValue('inn', $modelCompany->inn);
    $doc->setValue('ogrn', $modelCompany->ogrn);
    $doc->setValue('FIO', $modelProfile->fioFull);
    $doc->setValue('PASSPORT_ID', $modelPassport->number);
    $doc->setValue('PASSPORT_PLACE', $modelPassport->place);
    $doc->setValue('PASSPORT_DATE', $modelPassport->date);
    $doc->setValue('REG_ADDRESS', $modelProfile->reg_address);
    $doc->setValue('POA_DATE', $this->term_of_office);

//    ${PASSPORT_ID, выдан: ${PASSPORT_PLASE} ${PASSPORT_DATE}, прописанный по адресу ${REG_ADDRESS

//        $tmpPathDocx = 'tmp/tmp' . $this->id .'.docx';
    $tmpPathDocx = Yii::getAlias('@tmp/tmp' . $this->id_profile.'-'.$this->id_company .'.docx');
    $doc->saveAs($tmpPathDocx);
//
    $this->url_form = $this->id_profile.'-'.$this->id_company.'.pdf';
    if(Converter::file($tmpPathDocx) // Раскомментировать для удаленки! select a file for convertion
        ->setLibreofficeBinaryPath('/usr/bin/libreoffice') // Раскомментировать для удаленки! binary to the libreoffice binary
        ->setTemporaryPath(Yii::getAlias('@tmp')) // temporary directory for convertion
//          ->setTimeout(100) // libreoffice process timeout
        ->save(Yii::getAlias('@poa_forms/' .$this->url_form))) // save as pdf //Раскомментировать для удаленки!
        return $this;
    return false;
}

public function findByCompanyAndUser($idCompany, $idUser){
    return self::find()->where(['id_profile' => $idUser, 'id_company' => $idCompany])->one();
}

public function DeleteUploadPoaFile(){
    if ($this->url_upload_poa) {
//        foreach (unserialize($this->url_upload_poa) as $file) {
//            $file = Yii::getAlias('@poa_upload/' . $file);
//            if (file_exists($file)) unlink($file);
//        }
        $file = Yii::getAlias('@poa_upload/' .$this->url_upload_poa);
        if (file_exists($file)) unlink($file);
        else return $this;

        $this->url_upload_poa='';
//        $this->id = null;
        $this->STATUS_POA = self::STATUS_POWER_OF_ATTORNEY_UNSIGNED;
//        $this->term_of_office = null;
        $this->save();
        return $this;
    }
    return false;
}

    public static function getStatuses(){
        $statuses = [];
        $statuses [0] = 'Не подписан';
        $statuses [1] = 'Подписан';
        $statuses [2] = 'На проверке';
        $statuses [3] = 'Не прошел проверку';

        return $statuses;
    }

    public function getCompanyName(){
        return $this->company->name;
    }

    public function getFio(){
        return $this->profile->fioShort;
    }

    public function getFioFull(){
        return $this->profile->fioFull;
    }

    public function getUrlsUpload(){
        $urlsArray = unserialize($this->url_upload_poa);
        if(is_array($urlsArray)){
            $urlsHtml = '';
            foreach ($urlsArray as $url){
                $urlsHtml .= Html::a(
                        $url,
                        \yii\helpers\Url::to(['/poa/download-poa-on-check', 'url' => $url])
                    )
                    . '<br>';
            }
            return $urlsHtml;
        }
        return false;
    }

    public function UploadFilesExists(){
        foreach (unserialize($this->url_upload_poa) as $file){
            if(!file_exists(Yii::getAlias('@poa_upload/' . $file))) return false;
            return true;
        }
    }


    public function DeleteConfirmPoa(){
        if($this->url_poa && file_exists(Yii::getAlias('@poa_confirm/' . $this->url_poa))){
            unlink(Yii::getAlias('@poa_confirm/'.$this->url_poa));
            $this->url_poa = '';
            if($this->save()) return $this;
        }
        return $this;
    }
}
