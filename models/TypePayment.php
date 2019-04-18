<?php

namespace app\models;

use Yii;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "typies_payment".
 *
 * @property integer $id
 * @property string $type
 * @property string $min_text
 * @property string $description
 * @property string $textWithIconDiscount
 * @property string $minTextWithIconDiscount
 */
class TypePayment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'typies_payment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [['type', 'min_text'], 'string', 'max' => 255],
            ['description', 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
        ];
    }

    public function getTextWithIconDiscount(){
        return ($this->id == Payment::TYPE_BANK_TRANSFER)
                    ? $this->type
                    : Html::img('/img/icons/discount-16.png', ['title' => 'Действует скидка!']) . ' ' . $this->type;
    }

    public function getMinTextWithIconDiscount(){
        return ($this->id == Payment::TYPE_BANK_TRANSFER)
            ? $this->min_text
            : Html::img('/img/icons/discount-16.png', ['title' => 'Действует скидка!']) . ' ' . $this->type;
    }

    static public function getTypiesPaymentsArray(){
        $TypiesPayment = self::find()->all();
        foreach ($TypiesPayment as $item){
            $item->type = $item->getTextWithIconDiscount();
        }
        return ArrayHelper::map($TypiesPayment, 'id', 'type');

    }
}
