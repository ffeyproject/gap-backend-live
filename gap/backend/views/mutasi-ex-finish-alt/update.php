<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MutasiExFinishAlt */

$this->title = 'Update Mutasi Ex Finish Alt: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Mutasi Ex Finish Alts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="mutasi-ex-finish-alt-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
