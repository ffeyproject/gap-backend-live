<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\InspectingPrintingReject */

$this->title = 'Update Inspecting Printing Reject: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Inspecting Printing Rejects', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="inspecting-printing-reject-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
