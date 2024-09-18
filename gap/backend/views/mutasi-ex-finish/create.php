<?php
use common\models\ar\MutasiExFinishItem;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MutasiExFinish */
/* @var $modelsItem MutasiExFinishItem*/

$this->title = 'Create Mutasi Ex Finish';
$this->params['breadcrumbs'][] = ['label' => 'Mutasi Ex Finish', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mutasi-ex-finish-create">

    <?= $this->render('_form', [
        'model' => $model,
        'modelsItem' => $modelsItem
    ]) ?>

</div>
