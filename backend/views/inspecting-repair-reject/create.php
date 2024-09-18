<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\InspectingRepairReject */

$this->title = 'Create Inspecting Repair Reject';
$this->params['breadcrumbs'][] = ['label' => 'Inspecting Repair Rejects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inspecting-repair-reject-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
