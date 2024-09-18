<?php

/* @var $this yii\web\View */
/* @var $model common\models\ar\SuratJalanExFinish */

$this->title = 'Create Surat Jalan Ex Finish';
$this->params['breadcrumbs'][] = ['label' => 'Surat Jalan Ex Finishes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="surat-jalan-ex-finish-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
