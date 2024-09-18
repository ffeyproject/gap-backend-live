<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\MstVendor */

$this->title = 'Create Mst Vendor';
$this->params['breadcrumbs'][] = ['label' => 'Mst Vendors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-vendor-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
