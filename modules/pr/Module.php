<?php

namespace app\modules\pr;
use yii\filters\AccessControl;
/**
 * pr module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\pr\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->layout = 'pr';
        parent::init();

        // custom initialization code goes here
    }


    public function behaviors()
    {
        return [
            // Доступ к модулю только роли admin
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin']
                    ]
                ]
            ]
        ];
    }

}
