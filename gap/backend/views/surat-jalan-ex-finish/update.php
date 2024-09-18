<?php

/* @var $this yii\web\View */
/* @var $model common\models\ar\SuratJalanExFinish */

$this->title = 'Update Surat Jalan Ex Finish: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Surat Jalan Ex Finishes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="surat-jalan-ex-finish-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
