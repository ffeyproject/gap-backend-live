<?php

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstGreige */

$this->title = 'Update Greige: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Greiges', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="mst-greige-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
