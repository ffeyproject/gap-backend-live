<?php
namespace backend\components;

use Yii;

class Converter
{

    /**
     * @param $qty float
     * @return float
     */
    public static function meterToYard($qty){
        return $qty * Yii::$app->params['meterToYard'];
    }

    /**
     * @param $qty float
     * @return float
     */
    public static function yardToMeter($qty){
        return $qty * Yii::$app->params['yardToMeter'];
    }

    /**
     * @param $batchQty float
     * @param $perBatch float
     * @return float
     */
    public static function batchToUnit($batchQty, $perBatch){
        return $batchQty * $perBatch;
    }

    /**
     * @param $susut float
     * @param $unitQty float
     * @return float
     */
    public static function unitToFinish($susut, $unitQty){
        return $unitQty * (1 - ($susut/100));
    }
}