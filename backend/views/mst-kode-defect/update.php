<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\MstKodeDefect */

$this->title = 'Ubah Master Data Kode Defect: ' . $model->nama_defect;
$this->params['breadcrumbs'][] = ['label' => 'Master Data Kode Defect', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nama_defect, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>
<div class="mst-kode-defect-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>