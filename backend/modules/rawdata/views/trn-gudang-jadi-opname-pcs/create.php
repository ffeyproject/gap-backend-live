<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnGudangJadiOpnamePcs */

$this->title = 'Tambah Raw Data Gudang Jadi Stok Opname';
$this->params['breadcrumbs'][] = ['label' => 'Gudang Jadi Stok Opname', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-gudang-jadi-opname-pcs-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
