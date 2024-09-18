<?php

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnWo */

$this->title = 'Update Work Order: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Work Order', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="trn-wo-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
