<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 18.04.2018
 * Time: 12:39
 */

namespace app\models;

use Yii;
use yii\base\Model;

class DownloadPoaForm extends Model
{
    public $idPOA;
    public $dateTo;

    public function rules()
    {
        return [
            [['idPOA', 'dateTo'], 'required'],
            ['idPOA', 'string' ],
            ['dateTo', 'date', 'format' => 'php:d.m.Y']
        ];
    }

    public function attributeLabels()
    {
        return [
            'idPOA' => Yii::t('app', 'Номер доверенности:'),
            'dateTo' => Yii::t('app', 'Срок действия доверенности до:'),
        ];
    }

}