<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'queue'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\fixtures',
          ],
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager', // or use 'yii\rbac\PhpManager'
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
        
        'queue' => [
            'class' => \yii\queue\db\Queue::class,
            'db' => 'db', // harus sama seperti nama komponen 'db' (biasanya sudah ada di common/config/main-local.php)
            'tableName' => '{{%queue}}',
            'channel' => 'default',
            'mutex' => \yii\mutex\PgsqlMutex::class, // pakai PgsqlMutex jika pakai PostgreSQL
        ],
    ],
    'params' => $params,
];