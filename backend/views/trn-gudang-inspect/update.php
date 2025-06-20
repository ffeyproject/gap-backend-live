<?php
use common\models\ar\TrnGudangInspect;
use common\models\ar\TrnGudangInspectItem;

/* @var $this yii\web\View */
/* @var $model TrnGudangInspect */
/* @var $modelsItem TrnGudangInspectItem*/

$this->title = 'Update Packing List Gudang Inspect: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Packing List Gudang Inspect', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

<div class="trn-buy-pfp-update">
    <?= $this->render('_form-dua', [
        'model' => $model,
        'modelsStock' => $modelsStock
    ]) ?>
</div>
