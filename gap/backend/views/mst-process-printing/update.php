<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\MstProcessPrinting */

$this->title = 'Ubah Master Data Process Printing: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Master Data Process Printing', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>
<div class="mst-process-printing-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
