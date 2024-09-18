<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\MstHandling */

$this->title = 'Ubah Master Data Handling: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Master Data Handling', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>
<div class="mst-handling-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
