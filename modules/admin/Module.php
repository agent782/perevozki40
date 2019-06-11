<?php

namespace app\modules\admin;
use Yii;
use yii\filters\AccessControl;

/**
 * admin module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\admin\controllers';
//    public $viewPath = 'app\modules\admin\views';
    /**
     * @inheritdoc
     */

    public function behaviors()
    {
        return [
            // Доступ к модулю только роли admin
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin', '@']
                    ]
                ]
            ]
        ];
    }

    public function init()
    {
        //Yii::$app->setLayoutPath('app\modules\admin\views\layouts');
        //Yii::$app->layout = 'adminLayout';
        parent::init();

        // custom initialization code goes here
    }
}
