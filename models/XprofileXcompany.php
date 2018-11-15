<?php

namespace app\models;

use Yii;
use PhpOffice\PhpWord\PhpWord;
use Breadlesscode\Office\Converter;



/**
 * This is the model class for table "XprofileXcompany".
 *
 * @property integer $id_profile
 * @property integer $id_company
 * @property string $job_post
 * @property string $url_power_of_attorney
 * @property integer $term_of_office
 * @property integer $checked
 * @property string $url_form
 */
class XprofileXcompany extends \yii\db\ActiveRecord
{
//Статусы доверенности

    const STATUS_POWER_OF_ATTORNEY_UNSIGNED = 0;
    const STATUS_POWER_OF_ATTORNEY_SIGNED = 1;
    const STATUS_POWER_OF_ATTORNEY_ON_CHECKING = 2;
    const STATUS_POWER_OF_ATTORNEY_FAILED = 3;

    public $upload_files;
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
            [['id_profile', 'id_company'], 'integer'],
            [['job_post'], 'string', 'max' => 255],
            [['term_of_office', 'checked', 'STATUS_POWER_OF_ATTORNEY', 'url_power_of_attorney', 'url_form'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_profile' => Yii::t('app', 'Id Profile'),
            'id_company' => Yii::t('app', 'Id Company'),
            'job_post' => Yii::t('app', 'Должность'),
            'url_form' => Yii::t('app', 'Форса доверенности'),
            'url_power_of_attorney' => Yii::t('app', 'Доверенность'),
            'term_of_office' => Yii::t('app', 'Срок полномочий до'),
            'checked' => Yii::t('app', 'Проверено'),
            'STATUS_POWER_OF_ATTORNEY' => Yii::t('app', 'Статус'),
            'sTATUS_POWER_OF_ATTORNEY' => Yii::t('app', 'Доверенность'),
        ];
    }

    public function getSTATUS_POWER_OF_ATTORNEY(){
        switch ($this->STATUS_POWER_OF_ATTORNEY){
            case self::STATUS_POWER_OF_ATTORNEY_UNSIGNED:
                return 'Не подписана';
            case self::STATUS_POWER_OF_ATTORNEY_SIGNED:
                return 'Проверена';
            case self::STATUS_POWER_OF_ATTORNEY_ON_CHECKING:
                return 'На проверне';
            case self::STATUS_POWER_OF_ATTORNEY_FAILED:
                return 'Не прошла проверку или просрочена';
        }
    }
public function getCompany(){
    return $this->hasOne(Company::className(), ['id' => 'id_company']);
}
public function getProfile(){
    return $this->hasOne(Profile::className(), ['id_user'=>'id_profile']);
}

public function createPowerOfAttorneyForm(){
    $phpword =  new PhpWord();
    $modelCompany = $this->company;
    $modelProfile = $this->profile;
    $modelPassport = $modelProfile->passport;
    if(!$modelCompany) {
        Yii::$app->session->setFlash('errorCreatePOA', 'Должны быть заполнены все реквизиты юр.лица');
        return false;
    }
    if(!$modelProfile) {
        Yii::$app->session->setFlash('errorCreatePOA', 'Заполните все поля Профиля.');
        return false;
    }
    if(!$modelPassport) {
        Yii::$app->session->setFlash('errorCreatePOA', 'Заполните паспортные данные.');
        return false;
    }
    $doc = $phpword->loadTemplate('documents/templates/power_of_attorney.docx');

    $doc->setValue('id', $this->id_profile.'-'.$this->id_company );
    $doc->setValue('date', date('d.m.Y'));
    $doc->setValue('company_name', $modelCompany->name);
    $doc->setValue('company_address', $modelCompany->address);
    $doc->setValue('inn', $modelCompany->inn);
    $doc->setValue('ogrn', $modelCompany->ogrn);
//    $doc->setValue('PASSPORT_ID', $modelPassport->id);
    $doc->setValue('PASSPORT_PLACE', $modelPassport->place);
//    $doc->setValue('PASSPORT_DATE', $modelPassport->date);
    $doc->setValue('REG_ADDRESS', $modelProfile->reg_address);

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




}
