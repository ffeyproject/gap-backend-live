<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\InspectingRepairReject */

$this->title = 'Update Inspecting Repair Reject: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Inspecting Repair Rejects', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="inspecting-repair-reject-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
