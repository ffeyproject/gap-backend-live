<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\MstProcessPrinting */

$this->title = 'Tambah Master Data Process Printing';
$this->params['breadcrumbs'][] = ['label' => 'Master Data Process Printing', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-process-printing-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
