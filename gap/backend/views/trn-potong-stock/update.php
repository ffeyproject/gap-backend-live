<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnPotongStock */
/* @var $modelsItem common\models\ar\TrnPotongStockItem[] */

$this->title = 'Update Potong Stock Gudang Jadi: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Potong Stock Gudang Jadi', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="trn-potong-stock-update">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsItem' => $modelsItem
    ]) ?>

</div>
