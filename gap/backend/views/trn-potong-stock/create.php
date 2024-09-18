<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnPotongStock */
/* @var $modelsItem common\models\ar\TrnPotongStockItem[] */

$this->title = 'Create Potong Stock Gudang Jadi';
$this->params['breadcrumbs'][] = ['label' => 'Potong Stock Gudang Jadi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-potong-stock-create">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsItem' => $modelsItem
    ]) ?>

</div>
