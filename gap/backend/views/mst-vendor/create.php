<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstVendor */

$this->title = 'Create Vendor';
$this->params['breadcrumbs'][] = ['label' => 'Vendors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="mst-vendor-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
