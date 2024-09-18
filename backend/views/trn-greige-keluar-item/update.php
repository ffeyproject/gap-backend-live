<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnGreigeKeluarItem */

$this->title = 'Update Trn Greige Keluar Item: ' . $model->greige_keluar_id;
$this->params['breadcrumbs'][] = ['label' => 'Trn Greige Keluar Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->greige_keluar_id, 'url' => ['view', 'greige_keluar_id' => $model->greige_keluar_id, 'stock_greige_id' => $model->stock_greige_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="trn-greige-keluar-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
