<?php

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnMo */

$this->title = 'Update Marketing Order Printing: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Marketing Order', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="trn-mo-update">
    <?= $this->render('_form-printing', [
        'model' => $model,
    ]) ?>
</div>
