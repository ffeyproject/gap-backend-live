<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\MstKodeDefect */

$this->title = 'Tambah Master Data Kode Defect';
$this->params['breadcrumbs'][] = ['label' => 'Master Data Kode Defect', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-kode-defect-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>