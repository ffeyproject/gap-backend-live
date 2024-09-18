<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MutasiExFinishAlt */

$this->title = 'Create Mutasi Ex Finish Alt';
$this->params['breadcrumbs'][] = ['label' => 'Mutasi Ex Finish Alts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mutasi-ex-finish-alt-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
