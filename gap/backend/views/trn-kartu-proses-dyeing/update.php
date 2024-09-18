<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesDyeing */

$this->title = 'Update Kartu Proses Dyeing: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Kartu Proses Dyeing', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="trn-kartu-proses-dyeing-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
