<?php

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnGreigeKeluar */
/* @var $searchModel common\models\ar\TrnStockGreigeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $modelsItem \common\models\ar\TrnGreigeKeluarItem[] */

$this->title = 'Ubah Greige Keluar: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Greige Keluar', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>

<div class="trn-greige-keluar-update">
    <?= $this->render('_form', [
        'model' => $model,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'modelsItem' => $modelsItem
    ]) ?>
</div>
