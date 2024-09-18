<?php

use common\models\ar\TrnSc;
use common\models\ar\TrnScAgen;

/* @var $this yii\web\View */
/* @var $model TrnScAgen */

$formatter = Yii::$app->formatter;
$formatter->locale = 'en_GB';
$sc = $model->sc;

switch ($sc->tipe_kontrak){
    case TrnSc::TIPE_KONTRAK_LOKAL:
        echo $this->render('_loa-lokal', [
            'model' => $model,
            'formatter' => $formatter,
            'sc' => $sc
        ]);
        break;
    case TrnSc::TIPE_KONTRAK_EXPORT:
        echo $this->render('_loa-export', [
            'model' => $model,
            'formatter' => $formatter,
            'sc' => $sc
        ]);
        break;
}