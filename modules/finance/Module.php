<?php

namespace app\modules\finance;
use yii\filters\AccessControl;
/**
 * finance module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\finance\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->layout = 'finance';
        parent::init();

        // custom initialization code goes here
    }

//    public function behaviors()
//    {
//        return [
//            // Доступ к модулю только роли admin
//            'access' => [
//                'class' => AccessControl::className(),
//                'rules' => [
//                    [
//                        'allow' => true,
//                        'roles' => ['admin', 'finance', 'dispetcher']
//                    ]
//                ]
//            ]
//        ];
//    }

}
