<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKirimBuyerHeader */

$this->title = 'Ubah Pengiriman Ke Buyer: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Pengiriman Ke Buyer', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>
<div class="trn-kirim-buyer-header-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
