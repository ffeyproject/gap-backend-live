<?php

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstProcessPfp */

$this->title = 'Tambah Master Data Process PFP';
$this->params['breadcrumbs'][] = ['label' => 'Master Data Process PFP', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-process-pfp-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
