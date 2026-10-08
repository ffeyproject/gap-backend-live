<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnGudangJadiOpnamePcs */

$this->title = 'Ubah Raw Data Gudang Jadi Stok Opname: #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Gudang Jadi Stok Opname', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => '#' . $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>
<div class="trn-gudang-jadi-opname-pcs-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
