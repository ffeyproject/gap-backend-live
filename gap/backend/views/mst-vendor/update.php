<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstVendor */

$this->title = 'Update Vendor: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Vendors', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="mst-vendor-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
