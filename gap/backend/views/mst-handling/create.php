<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\MstHandling */

$this->title = 'Tambah Master Data Handling';
$this->params['breadcrumbs'][] = ['label' => 'Master Data Handling', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-handling-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
