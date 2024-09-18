<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\GudangJadiMutasi */
/* @var $searchModel common\models\ar\TrnStockGreigeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $modelsItem common\models\ar\GudangJadiMutasiItem[] */

$this->title = 'Update Gudang Jadi Mutasi: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Gudang Jadi Mutasis', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

<div class="gudang-jadi-mutasi-update">
    <?= $this->render('_form', [
        'model' => $model,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'modelsItem' => $modelsItem
    ]) ?>
</div>
