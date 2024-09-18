<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnPfpKeluarItem */

$this->title = 'Update Trn Pfp Keluar Item: ' . $model->pfp_keluar_id;
$this->params['breadcrumbs'][] = ['label' => 'Trn Pfp Keluar Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->pfp_keluar_id, 'url' => ['view', 'pfp_keluar_id' => $model->pfp_keluar_id, 'stock_pfp_id' => $model->stock_pfp_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="trn-pfp-keluar-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
