<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnStockGreige */

$this->title = 'Update Trn Stock Greige: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Trn Stock Greiges', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="trn-stock-greige-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
