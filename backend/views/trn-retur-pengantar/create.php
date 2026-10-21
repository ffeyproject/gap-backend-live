<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnReturPengantar */
/* @var $selectedIds array */

$this->title = 'Tambah Surat Pengantar Retur';
$this->params['breadcrumbs'][] = ['label' => 'Surat Pengantar Retur', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-retur-pengantar-create">
    <?= $this->render('_form', [
        'model' => $model,
        'selectedIds' => $selectedIds,
    ]) ?>
</div>
