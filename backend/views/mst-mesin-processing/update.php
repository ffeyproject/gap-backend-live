<?php

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstMesinProcessing */

$this->title = 'Update Mesin Processing: ' . $model->nama_mesin;
$this->params['breadcrumbs'][] = ['label' => 'Mesin Processing', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nama_mesin, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

<div class="mst-mesin-processing-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
