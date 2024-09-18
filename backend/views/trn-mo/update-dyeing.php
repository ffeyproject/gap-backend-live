<?php

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnMo */

$this->title = 'Update Marketing Order Dyeing: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Marketing Order', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="trn-mo-update">
    <?= $this->render('_form-dyeing', [
        'model' => $model,
    ]) ?>
</div>
