<?php
return [
    'on beforeAction' => function ($event) {
        if (Yii::$app->has('db') && Yii::$app->db) {
            try {
                $context = Yii::$app->request->isConsoleRequest ? 'CLI: ' . implode(' ', $_SERVER['argv']) : Yii::$app->request->url;
                $userId = (Yii::$app->has('user') && !Yii::$app->user->isGuest) ? Yii::$app->user->id : null;
                
                Yii::$app->db->createCommand("SET app.context = :context", [':context' => $context])->execute();
                if ($userId !== null) {
                    Yii::$app->db->createCommand("SET app.user_id = :userId", [':userId' => $userId])->execute();
                }
            } catch (\Exception $e) {
                // Ignore DB connection errors if DB is not ready or open
            }
        }
    },
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