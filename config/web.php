<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'homeUrl' => array('users/index'),
    'bootstrap' => ['log'],
    'defaultRoute' => 'users/index',
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'DAS451hDhg45asd87fDShdgfR5sdaugfa5asd7asrfa78aw5ere',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'app\models\Users',
            'loginUrl' => 'index',
            'enableAutoLogin' => true,
        ],
        'admin' => [
            'class' => 'yii\web\User',
            'identityClass' => 'app\models\Admins',
            'loginUrl' => 'login',
            'idParam' => '__adminId',
            'identityCookie' => ['name' => '_adminIdentity', 'httpOnly' => true]
        ],
        'errorHandler' => [
            'errorAction' => 'users/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => FALSE,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com', // e.g. smtp.mandrillapp.com or smtp.gmail.com
                'username' => 'aram.stdev@gmail.com',
                'password' => 'aram094678798',
                'port' => '587', // Port 25 is a very common port too
                'encryption' => 'tls', // It is often used, check your provider or mail server specs
            ],
        ],
        'reCaptcha' => [
            'name' => 'reCaptcha',
            'class' => 'himiklab\yii2\recaptcha\ReCaptcha',
            'siteKey' => '6Lf3WQwTAAAAAGhg2foSOsAGOH2SkLYRelMiGpt6',
            'secret' => '6Lf3WQwTAAAAAFiaEQSiUbSQBaxntYOJKxN5pUDU',
        ],
        'session' => [
            'class' => 'yii\web\CacheSession',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>/<key:\w+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>/<id:\w+>/<param:\w+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'authManager' => [
            'class' => 'yii\rbac\PhpManager',
            'defaultRoles' => ['admin', 'user'],
        ],
        'bundles' => [
            'class' => 'yii\bootstrap\BootstrapAsset',
            'yii\bootstrap\BootstrapAsset' => [
                'css' => [],
            ]
        ],
        'FileUploader' => [
            'class' => 'app\components\FileUploader',
        ]
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
//    $config['bootstrap'][] = 'debug';
//    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
