<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $user->verification_token]);
?>
<div class="verify-email">
    <p>Hello <?= Html::encode($user->username) ?>,</p>

    <p>Anda diundang untuk menjadi admin di software gajahtex, silahkan klik link berikut ini untuk mengaktifkan akun anda:</p>

    <p><?= Html::a(Html::encode($verifyLink), $verifyLink) ?></p>
</div>
