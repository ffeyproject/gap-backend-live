<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKirimBuyerHeader */

$tab = $model->is_export ? 'export' : 'lokal';
$subTitle = $model->is_export ? 'Export' : 'Lokal';
$this->title = 'Buat Pengiriman Ke Buyer (' . $subTitle . ')';
$this->params['breadcrumbs'][] = ['label' => 'Pengiriman Ke Buyer', 'url' => ['index', 'tab' => $tab]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kirim-buyer-header-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
