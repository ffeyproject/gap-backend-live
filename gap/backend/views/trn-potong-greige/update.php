<?php

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnPotongGreige */
/* @var $modelsItem common\models\ar\TrnPotongGreigeItem[] */

$this->title = 'Ubah Potong Greige: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Potong Greige', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>

<div class="trn-potong-greige-update">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsItem' => $modelsItem
    ]) ?>
</div>
