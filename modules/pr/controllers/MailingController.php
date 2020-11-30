<?php

namespace app\modules\pr\controllers;

use app\models\MailingForm;
use app\models\User;

class MailingController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $MailingForm = new MailingForm();
        $users  - User::find()->all();
        foreach ($users as $user){

        }

        return $this->render('index', [
            'MailingForm' => $MailingForm
        ]);
    }

}
