<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\MutasiExFinish */

$this->title = 'Create Mutasi Ex Finish';
$this->params['breadcrumbs'][] = ['label' => 'Mutasi Ex Finishes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mutasi-ex-finish-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
