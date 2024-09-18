<?php
use common\models\ar\MutasiExFinishItem;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MutasiExFinish */
/* @var $modelsItem MutasiExFinishItem*/

$this->title = 'Update Mutasi Ex Finish: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Mutasi Ex Finish', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="mutasi-ex-finish-update">

    <?= $this->render('_form', [
        'model' => $model,
        'modelsItem' => $modelsItem
    ]) ?>

</div>
