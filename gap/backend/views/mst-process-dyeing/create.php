<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\MstProcessDyeing */

$this->title = 'Tambah Master Data Process Dyeing';
$this->params['breadcrumbs'][] = ['label' => 'Master Data Process Dyeing', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="mst-process-dyeing-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
