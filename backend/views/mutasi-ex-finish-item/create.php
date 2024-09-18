<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MutasiExFinishItem */

$this->title = 'Create Mutasi Ex Finish Item';
$this->params['breadcrumbs'][] = ['label' => 'Mutasi Ex Finish Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mutasi-ex-finish-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
