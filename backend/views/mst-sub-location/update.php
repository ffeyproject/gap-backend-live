<?php

$this->title = 'Update Sub Location: ' . $model->locs_code;
$this->params['breadcrumbs'][] = ['label' => 'Sub Location', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->locs_code, 'url' => ['view', 'id' => $model->locs_code]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="mst-sub-location-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
