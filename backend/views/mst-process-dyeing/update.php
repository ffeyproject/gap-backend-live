<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\MstProcessDyeing */

$this->title = 'Ubah Master Data Process Dyeing: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Master Data Process Dyeing', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>

<div class="mst-process-dyeing-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
