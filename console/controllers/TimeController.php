<?php

namespace console\controllers;

use yii\console\Controller;

class TimeController extends Controller
{
    public function actionNow()
    {
        echo "Timezone Yii  : " . \Yii::$app->timeZone . "\n";
        echo "PHP Now       : " . date('Y-m-d H:i:s') . "\n";
        echo "Yii Formatter : " . \Yii::$app->formatter->asDatetime('now') . "\n";
    }
}