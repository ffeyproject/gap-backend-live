<?php

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstCustomer */

$this->title = 'Update Customer: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Customers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="mst-customer-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
