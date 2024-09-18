<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnStockGreige */

$this->title = 'Buat Packing List Greige';
$this->params['breadcrumbs'][] = ['label' => 'Packing List Greige', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-stock-greige-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
