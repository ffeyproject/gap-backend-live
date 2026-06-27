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
        'db' => [
            'on afterOpen' => function ($event) {
                static $inAfterOpen = false;
                if ($inAfterOpen) {
                    return;
                }
                $inAfterOpen = true;
                try {
                    $context = Yii::$app->request->isConsoleRequest ? 'CLI: ' . implode(' ', $_SERVER['argv']) : Yii::$app->request->url;
                    $event->sender->createCommand("SET app.context = :context", [':context' => $context])->execute();
                    
                    if (Yii::$app->has('user') && Yii::$app->user && Yii::$app->user->hasProperty('identity') && Yii::$app->user->identity) {
                        $userId = Yii::$app->user->id;
                        $event->sender->createCommand("SET app.user_id = :userId", [':userId' => $userId])->execute();
                    }
                } catch (\Exception $e) {
                    // Safe fallback
                } finally {
                    $inAfterOpen = false;
                }
            },
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],

        'queue' => [
        'class' => \yii\queue\db\Queue::class,
        'db' => 'db',
        'tableName' => '{{%queue}}',
        'channel' => 'default',
        'mutex' => \yii\mutex\PgsqlMutex::class,
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