<?php

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstGreige */

$this->title = 'Update Location: ' . $model->loc_name;
$this->params['breadcrumbs'][] = ['label' => 'Location', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->loc_id, 'url' => ['view', 'id' => $model->loc_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="mst-location-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
