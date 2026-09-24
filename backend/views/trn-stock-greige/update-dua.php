<?php
use backend\models\ar\StockGreige;
use backend\models\form\StockGreigeForm;

/* @var $this yii\web\View */
/* @var $model StockGreigeForm */
/* @var $modelsStock StockGreige[] */

$this->title = 'Ubah Packing List Greige: ' . $model->no_document;
$this->params['breadcrumbs'][] = ['label' => 'Packing List Greige', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->no_document, 'url' => ['view-doc', 'no_doc' => $model->no_document]];
$this->params['breadcrumbs'][] = 'Ubah';
?>
<div class="trn-stock-greige-update-dua">
    <?= $this->render('_form-dua', [
        'model' => $model,
        'modelsStock' => $modelsStock
    ]) ?>
</div>
