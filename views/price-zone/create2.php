<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 11.07.2018
 * Time: 9:28
 */
use yii\bootstrap\ActiveForm;
use app\models\Vehicle;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Html;
use yii\helpers\Url;
//var_dump($model);

$form = ActiveForm::begin([

]);

//switch ($model->veh_type) {
//    case Vehicle::TYPE_TRUCK:
//        echo 111111;
//        break;
//    case Vehicle::TYPE_PASSENGER:
//        echo 222222;
//        break;
//    case Vehicle::TYPE_SPEC:

$body_typies= \app\models\BodyType::find()->where(['id_type_vehicle' => $model->veh_type])
    ->orderBy(['body' => SORT_ASC])
    ->all();

echo ($model->veh_type != Vehicle::TYPE_SPEC)? $form->field($model, 'body_types[]')->checkboxList(
    ArrayHelper::map(
        $body_typies, 'id', 'body')):
    $form->field($model, 'body_types')->radioList(
        ArrayHelper::map(
            $body_typies, 'id', 'body'));

echo Html::a('Отмена', Url::to('/price-zone'), [
        'class' => 'btn btn-info',
    ]).' ';
echo Html::submitButton('Далее', [
    'id' => 'next2',
    'name' => 'button',
    'value' => 'next2',
    'class' => 'btn btn-success'
]);

ActiveForm::end();