<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\MutasiExFinishItem */

$this->title = 'Update Mutasi Ex Finish Item: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Mutasi Ex Finish Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="mutasi-ex-finish-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
