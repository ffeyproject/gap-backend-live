<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnPfpKeluar */
/* @var $searchModel common\models\ar\TrnStockGreigeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $modelsItem common\models\ar\TrnPfpKeluarItem[] */

$this->title = 'Ubah PFP Keluar: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'PFP Keluar', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>
<div class="trn-pfp-keluar-update">
    <?= $this->render('_form', [
        'model' => $model,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'modelsItem' => $modelsItem
    ]) ?>

</div>
