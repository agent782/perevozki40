<?php

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');
//$dadata = 'c53e6a2d52a60943309905a9e6b83c9e5d392eae';
$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'sourceLanguage' => 'ru-RU',
    'language'=>'ru',
    'defaultRoute' => 'default/index',
    'layout' => 'default2',
    'modules' => [
        'admin' => [
            'class' => 'app\modules\admin\Module',
        ],
        'manager' => [
          'class' => 'app\modules\manager\Module'
        ],
        'client2' => [
            'class' => 'app\modules\client2\Module'
        ],
        'finance' => [
            'class' => 'app\modules\finance\Module',
        ],
    ],
    //'layout' => 'testLayout',
    'components' => [
        'session' => [
            'class' => 'yii\web\DbSession',
            // 'db' => 'mydb',  // ID компонента для взаимодействия с БД. По умолчанию 'db'.
            // 'sessionTable' => 'my_session', // название таблицы для хранения данных сессии. По умолчанию 'session'.
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '10081985',
            'baseUrl' => '',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => 'default/login',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logVars' => ['_GET', '_POST'],
                ],
            ],
        ],
        'db' => $db,
        'authManager' => [
            'class'           => 'yii\rbac\DbManager',
//            'itemTable'       => 'auth_item',
//            'itemChildTable'  => 'auth_item_child',
//            'assignmentTable' => 'auth_assignment',
//            'ruleTable'       => 'auth_rule',
//            'defaultRoles'    => ['guest'],// роль которая назначается всем пользователям по умолчанию
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
//            'rules' => [
//            ],
        ],
        'encrypter' => [
            'class'=>'\nickcv\encrypter\components\Encrypter',
            'globalPassword'=>'k031208m',
            'iv'=>'григоров',
            'useBase64Encoding'=>true,
            'use256BitesEncoding'=>false,
        ],
//        'response' => [
//            'formatters' => [
//                'pdf' => [
//                    'class' => 'robregonm\pdf\PdfResponseFormatter',
//                ],
//            ]
//        ],

    ],
    'params' => $params,
    'aliases' =>[
        '@userPhotoDir' => '@app/web/uploads/photos',
        '@uploads' => '@app/web/uploads/',
//        '@tcpdf' => '@vendor/tecnickcom/tcpdf',
//        '@dompdf' => '@vendor/dompdf/dompdf',
//        '@mpdf' => '@vendor/mpdf/mpdf',
        '@client_contracts_forms' => '@app/web/documents/client_contracts',
        '@client_contracts_uploads' => '@app/web/uploads/documents/client-contract',
        '@client_contracts_confirm' => '@app/web/documents/client-contract-confirm',
        '@tmp' => '@app/web/tmp',
        '@poa_forms' => '@app/web/documents/poa/forms', //power_of_attorney
        '@poa_confirm' => '@app/web/documents/poa/confirm', //power_of_attorney
    ]
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];


    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];
}

return $config;
