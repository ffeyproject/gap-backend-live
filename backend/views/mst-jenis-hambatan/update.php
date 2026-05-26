<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\MstJenisHambatan */

$this->title = 'Ubah Master Jenis Hambatan: ' . $model->nama;
$this->params['breadcrumbs'][] = ['label' => 'Master Jenis Hambatan Mesin', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nama, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>
<div class="mst-jenis-hambatan-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
