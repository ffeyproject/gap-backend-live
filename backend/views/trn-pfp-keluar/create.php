<?php

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnPfpKeluar */
/* @var $searchModel common\models\ar\TrnStockGreigeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tambah PFP Keluar';
$this->params['breadcrumbs'][] = ['label' => 'PFP Keluar', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-pfp-keluar-create">

    <?= $this->render('_form', [
        'model' => $model,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]) ?>

</div>
