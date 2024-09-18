<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MutasiExFinishAltItem */

$this->title = 'Create Mutasi Ex Finish Alt Item';
$this->params['breadcrumbs'][] = ['label' => 'Mutasi Ex Finish Alt Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mutasi-ex-finish-alt-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
