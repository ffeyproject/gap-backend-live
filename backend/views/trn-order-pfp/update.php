<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnOrderPfp */

$this->title = 'Update Order PFP: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Order PFP', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="trn-order-pfp-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
