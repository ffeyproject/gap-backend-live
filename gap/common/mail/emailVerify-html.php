<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $user->verification_token]);
?>
<div class="verify-email">
    <p>Hello <?= Html::encode($user->username) ?>,</p>

    <p>Kami telah menerima pendaftaran akun anda. Silakan klik link berikut untuk mengaktifkan akun dan melengkapi data
        diri
        (nama lengkap dan password):
    </p>

    <p><?= Html::a(Html::encode($verifyLink), $verifyLink) ?></p>
</div>