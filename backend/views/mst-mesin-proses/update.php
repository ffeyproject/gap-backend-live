<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\MstMesinProses */

$this->title = 'Ubah Master Mesin Proses: ' . $model->nama_mesin;
$this->params['breadcrumbs'][] = ['label' => 'Master Mesin Proses Processing', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nama_mesin, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>
<div class="mst-mesin-proses-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
