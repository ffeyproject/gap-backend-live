<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'name' => 'GAP V2 BACKEND ERP',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'queue'],
    'controllerNamespace' => 'backend\controllers',
    'modules' => [
        'admin' => [
            'class' => 'mdm\admin\Module',
            //'layout' => 'left-menu',
            'mainLayout' => '@backend/views/layouts/main.php',
        ],
        'user' => [
            'class' => 'backend\modules\user\Module',
        ],
        'gridview' =>  [
            'class' => '\kartik\grid\Module'
        ],
        'direktur' => [
            'class' => 'backend\modules\direktur\Module',
        ],
        'rawdata' => [
            'class' => 'backend\modules\rawdata\Module',
        ],
        'reset' => [
            'class' => 'backend\modules\reset\Module',
        ],
        'db-manager' => [
            'class' => 'bs\dbManager\Module',
            // path to directory for the dumps
            'path' => '@backend/backups',
            // list of registerd db-components
            'dbList' => ['db'],
            /*'as access' => [
                'class' => 'yii\filters\AccessControl',
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],*/
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_gap2_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_gap_v2_identity-backend', 'httpOnly' => true],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager', // or use 'yii\rbac\PhpManager'
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'gap_v2-advanced-backend',
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'queue' => [
        'class' => \yii\queue\db\Queue::class,
        'db' => 'db',
        'tableName' => '{{%queue}}',
        'channel' => 'default',
        'mutex' => \yii\mutex\PgsqlMutex::class,
        ],

        'urlManager' => [
        'class' => 'yii\web\UrlManager',
        'hostInfo' => 'http://live.produksionline.xyz', // ganti sesuai domain
        'baseUrl' => (isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] . '://' : 'http://') . $_SERVER['SERVER_NAME'] . (($_SERVER['SERVER_PORT'] !== '80') ? ':' . $_SERVER['SERVER_PORT'] : ''),
        'enablePrettyUrl' => true,
        'showScriptName' => false,
        ],

        'mailer' => [
        'class' => 'yii\swiftmailer\Mailer',
        'viewPath' => '@common/mail',
        'useFileTransport' => false,
        'transport' => [
            'class' => 'Swift_SmtpTransport',
            'host' => 'smtp.gmail.com',
            'username' => 'infogajahapp@gmail.com',
            'password' => 'antoiweirmindnhm',
            'port' => '587',
            'encryption' => 'tls',
            ],
        ],

        //Setting Jika VPS Menggunakan IPV6
        
        // 'mailer' => [
        // 'class' => 'yii\swiftmailer\Mailer',
        // 'viewPath' => '@common/mail',
        // 'useFileTransport' => false,
        // 'transport' => [
        //     'class' => 'Swift_SmtpTransport',
        //     'host' => '142.251.10.109',
        //     'username' => 'infogajahapp@gmail.com',
        //     'password' => 'xqrruhqtucxioqxl',
        //     'port' => '587',
        //     'encryption' => 'tls',
	    //    'streamOptions' => [
        //        'ssl' => [
        //        'verify_peer' => false,
        //        'verify_peer_name' => false,
        //        'allow_self_signed' => true,
        //       ],
        //      ],
        //     ],
        // ],


        // 'urlManager' => [
        //     'enablePrettyUrl' => true,
        //     'showScriptName' => false,
        //     'rules' => [
        //     ],
        // ],
    ],
    'params' => $params,
    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
            'site/login',
            'site/verify-email',
            'site/request-password-reset',
            'site/reset-password',
            'site/error',
            'test/index',
            'dep-drop/*',
            'ajax/*',
            //'admin/*',
            // The actions listed here will be allowed to everyone including guests.
            // So, 'admin/*' should not appear here in the production, of course.
            // But in the earlier stages of your development, you may probably want to
            // add a lot of actions here until you finally completed setting up rbac,
            // otherwise you may not even take a first step.
        ]
    ],

    
];