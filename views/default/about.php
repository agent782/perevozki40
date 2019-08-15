<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 14.08.2019
 * Time: 13:53
 */
use yii\bootstrap\Tabs;
?>

<div class="container contacts">
    <label class="h3">
        О сервисе.
    </label>
    <?= Tabs::widget([
        'items' => [
            [
                'label' => 'Клиентам',
                'content' => $this->render('/default/about_for_client')
            ],
            [
                'label' => 'Водителям',
                'content' => $this->render('/default/about_for_car_owner')
            ]
        ]
    ])?>
</div>


