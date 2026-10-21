<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnReturPengantar */
/* @var $selectedIds array */

$this->title = 'Ubah Surat Pengantar Retur: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Surat Pengantar Retur', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>
<div class="trn-retur-pengantar-update">
    <?= $this->render('_form', [
        'model' => $model,
        'selectedIds' => $selectedIds,
    ]) ?>
</div>
