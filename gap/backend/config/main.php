<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'name' => 'GAP V2 BACKEND',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
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
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
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
