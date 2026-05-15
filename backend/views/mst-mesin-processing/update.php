<?php

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstMesinProcessing */

$namaMesinStr = is_array($model->nama_mesin) ? implode(', ', $model->nama_mesin) : $model->nama_mesin;
$this->title = 'Update Mesin Processing: ' . $namaMesinStr;
$this->params['breadcrumbs'][] = ['label' => 'Mesin Processing', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $namaMesinStr, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

<div class="mst-mesin-processing-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
