<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKirimBuyer */

$this->title = 'Buat Pengiriman Ke Buyer';
$this->params['breadcrumbs'][] = ['label' => 'Pengiriman Ke Buyer', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kirim-buyer-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
