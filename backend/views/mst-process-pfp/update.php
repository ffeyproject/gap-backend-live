<?php

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstProcessPfp */

$this->title = 'Ubah Master Data Process PFP: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Master Data Process PFP', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>
<div class="mst-process-pfp-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
