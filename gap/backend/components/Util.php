<?php
namespace backend\components;

use Yii;
use yii\helpers\Html;

class Util
{
    /**
     * @return string
     * @throws \yii\base\Exception
     */
    public static function getHiddenFormTokenField() {
        $token = Yii::$app->getSecurity()->generateRandomString();
        $token = str_replace('+', '.', base64_encode($token));

        Yii::$app->session->set(Yii::$app->params['form_token_param'], $token);
        return Html::hiddenInput(Yii::$app->params['form_token_param'], $token);
    }
}