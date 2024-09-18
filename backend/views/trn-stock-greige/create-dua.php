<?php
use backend\models\ar\StockGreige;
use backend\models\form\StockGreigeForm;

/* @var $this yii\web\View */
/* @var $model StockGreigeForm */
/* @var $modelsStock StockGreige[] */

$this->title = 'Buat Packing List Greige';
$this->params['breadcrumbs'][] = ['label' => 'Packing List Greige', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-stock-greige-create">
    <?= $this->render('_form-dua', [
        'model' => $model,
        'modelsStock'=>$modelsStock
    ]) ?>
</div>
