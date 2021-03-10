<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 09.01.2019
 * Time: 15:02
 */
    use yii\bootstrap\ActiveForm;
    use yii\bootstrap\Html;
    use yii\helpers\Url;

?>
<div class="container">
    <div class="row">
<div class="col-lg-6">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($OrderModel, 'id_vehicle')->radioList($vehicles
        ,[
            'encode' => false,
//            'value' => $id_vehicle
        ]
    )->label('Выберите ТС')?>

    <?php
        if (!$UserModel->getDrivers()->count() && !$Profile->is_driver){
            echo $form->field($Profile, 'is_driver')->checkbox()->label('Водитель - ' . $Profile->fioFull);
        }
    ?>
    <?= $form->field($OrderModel, 'id_driver')->radioList($drivers, [])
        ->label('Выберите водителя ' . Html::a(Html::icon('plus', [
                'class' => 'btn btn-info',
                'title' => 'Добавить водителя'
            ]), ['/driver/create',
                'id_car_owner' => $id_user,
                'redirect' => Url::to([
                    '/order/accept-order',
                    'id_order' => $OrderModel->id,
//                    'id_vehicle' => $id_vehicle,
                    'id_user' => $id_user,
                    'redirect' => $redirect,
                ])
            ])); ?>
    <br>
    <?= Html::a('Назад', $redirect, ['class' => 'btn btn-warning'])?>
    <?= Html::submitButton('Принять и позвонить', ['class' => 'btn btn-primary'])?>
    <?php
        ActiveForm::end();
    ?>
</div>
        <br><br>
<div class="col-lg-6">
    <?= $OrderModel->getFullNewInfo(false, false, false)?>
</div>
</div>
</div>
