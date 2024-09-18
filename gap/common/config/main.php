<?php
return [
    'timeZone' => 'Asia/Jakarta',
    'language' => 'id-ID',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'mailer' => [
            'class' => \yii\swiftmailer\Mailer::className(),
            'viewPath' => '@common/mail',
            'useFileTransport' => false,
            'transport' => [
                'class' => Swift_SmtpTransport::class,
                'host' => 'smtp.gmail.com',
                'username' => 'gajahapp@gmail.com',
                'password' => 'Gajah823',
                'port' => '587',
                'encryption' => 'tls',
            ]
        ],
        'mailer_pmc' => [
            'class' => \yii\swiftmailer\Mailer::className(),
            'viewPath' => '@common/mail',
            'useFileTransport' => false,
            'transport' => [
                'class' => Swift_SmtpTransport::class,
                'host' => 'smtp.gmail.com',
                'username' => 'pmc1gap@gmail.com',
                'password' => 'gajah823',
                'port' => '587',
                'encryption' => 'tls',
            ]
        ],
    ],
];
