<?php

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstCustomer */

$this->title = 'Create Customer';
$this->params['breadcrumbs'][] = ['label' => 'Customers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-customer-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
